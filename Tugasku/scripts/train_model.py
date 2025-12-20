import argparse
import sys
import json
import os
import pandas as pd
import numpy as np
import time
import joblib 

try:
    import tensorflow as tf
    from tensorflow.keras.models import load_model, Sequential
    from tensorflow.keras.layers import Dense, Dropout, Conv1D, MaxPooling1D, Flatten
    from sklearn.model_selection import train_test_split
    from sklearn.preprocessing import LabelEncoder, StandardScaler
    
    import matplotlib.pyplot as plt
    plt.switch_backend('Agg') 
except ImportError:
    print(json.dumps({"error": "Library kurang. Install: pip install tensorflow pandas scikit-learn matplotlib joblib"}))
    sys.exit(1)

# --- FUNGSI BANTUAN ---
def clean_and_encode_data(df):
    for col in df.columns: 
        if df[col].dtype == 'object':
            le = LabelEncoder()
            df[col] = le.fit_transform(df[col].astype(str))
    return df

def save_training_plot(history, plot_path):
    acc = history.history['accuracy']
    loss = history.history['loss']
    epochs_range = range(1, len(acc) + 1)

    plt.figure(figsize=(10, 5))
    plt.subplot(1, 2, 1)
    plt.plot(epochs_range, acc, label='Accuracy', color='blue')
    plt.title('Accuracy')
    plt.grid(True)

    plt.subplot(1, 2, 2)
    plt.plot(epochs_range, loss, label='Loss', color='red')
    plt.title('Loss')
    plt.grid(True)

    plt.tight_layout()
    plt.savefig(plot_path)
    plt.close()

# --- FUNGSI TRAINING ---
def train_model(args):
    df = pd.read_csv(args.dataset)
    df = clean_and_encode_data(df)
    
    X = df.iloc[:, :-1].values
    y = df.iloc[:, -1].values
    
    # Encode Target
    y_le = LabelEncoder() 
    if y.dtype == 'object':
        y = y_le.fit_transform(y)
  
    # Scaling & Simpan Rumusnya
    sc = StandardScaler()
    X = sc.fit_transform(X)
    joblib.dump(sc, args.scaler_file) 

    # Reshape jika CNN
    if args.type == 'CNN':
        X = X.reshape(X.shape[0], X.shape[1], 1)

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    model = Sequential()
    
    if args.type == 'ANN':
        model.add(Dense(64, activation='relu', input_shape=(X_train.shape[1],))) 
        model.add(Dropout(0.2))
        model.add(Dense(32, activation='relu'))
    elif args.type == 'CNN':
        model.add(Conv1D(filters=32, kernel_size=2, activation='relu', input_shape=(X_train.shape[1], 1)))
        if X_train.shape[1] > 2: model.add(MaxPooling1D(pool_size=2))
        model.add(Flatten())
        model.add(Dense(64, activation='relu'))

    unique_classes = len(np.unique(y))
    if unique_classes > 2:
        model.add(Dense(unique_classes, activation='softmax'))
        loss_fn = 'sparse_categorical_crossentropy'
    else:
        model.add(Dense(1, activation='sigmoid'))
        loss_fn = 'binary_crossentropy'

    optimizer = tf.keras.optimizers.Adam(learning_rate=args.lr)
    model.compile(optimizer=optimizer, loss=loss_fn, metrics=['accuracy'])

    history = model.fit(X_train, y_train, epochs=args.epochs, batch_size=args.batch_size, verbose=0, validation_data=(X_test, y_test))
    
    loss, accuracy = model.evaluate(X_test, y_test, verbose=0)
    
    save_training_plot(history, args.plot_file)
    model.save(args.model_file)

    return {
        "status": "success",
        "accuracy": round(accuracy * 100, 2),
        "loss": round(loss, 4)
    }

# --- FUNGSI PREDIKSI (DENGAN CONFIDENCE SCORE) ---
def predict_model(args):
    # 1. Load Model & Scaler
    if not os.path.exists(args.model_file) or not os.path.exists(args.scaler_file):
        raise ValueError("Model atau Scaler tidak ditemukan. Lakukan Training dulu!")

    model = load_model(args.model_file)
    sc = joblib.load(args.scaler_file)

    # 2. Baca Data Baru
    df = pd.read_csv(args.dataset)
    df_clean = clean_and_encode_data(df.copy())
    X_new = df_clean.values

    # Cek Jumlah Kolom (Fix Error 32 vs 31)
    if hasattr(sc, 'n_features_in_'):
        required_features = sc.n_features_in_
        if X_new.shape[1] == required_features + 1:
            X_new = X_new[:, :-1]
        elif X_new.shape[1] != required_features:
            raise ValueError(f"Jumlah kolom tidak cocok! Butuh {required_features}, ada {X_new.shape[1]}.")

    # 3. Scaling
    X_new = sc.transform(X_new)

    # 4. Reshape jika CNN
    input_shape = model.input_shape
    if len(input_shape) == 3: 
        X_new = X_new.reshape(X_new.shape[0], X_new.shape[1], 1)

    # 5. PREDIKSI
    # verbose=0 agar tidak nyampah log
    predictions = model.predict(X_new, verbose=0)
    
    final_results = []
    
    # Ambil 10 data pertama saja
    for i in range(min(10, len(predictions))):
        pred_raw = predictions[i]
        
        # Logika Binary (2 Kelas) vs Multi-Class
        if len(pred_raw) == 1:
            # Binary Classification
            score = float(pred_raw[0])
            predicted_class = 1 if score > 0.5 else 0
            # Jika tebak 1, yakin = score. Jika tebak 0, yakin = 1 - score
            confidence = score if predicted_class == 1 else 1 - score
        else:
            # Multi-Class Classification
            predicted_class = int(np.argmax(pred_raw))
            confidence = float(np.max(pred_raw))

        final_results.append({
            "class": int(predicted_class),
            "confidence": round(confidence * 100, 2) # Ubah ke persen
        })

    return {
        "status": "success",
        "predictions": final_results
    }

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--mode', type=str, choices=['train', 'predict'], required=True)
    parser.add_argument('--dataset', type=str, required=True)
    parser.add_argument('--model_file', type=str, required=True)
    parser.add_argument('--scaler_file', type=str, required=True)
    
    parser.add_argument('--type', type=str, default='ANN')
    parser.add_argument('--epochs', type=int, default=10)
    parser.add_argument('--batch_size', type=int, default=32)
    parser.add_argument('--lr', type=float, default=0.001)
    parser.add_argument('--plot_file', type=str, default='')

    args = parser.parse_args()

    try:
        if args.mode == 'train':
            result = train_model(args)
        else:
            result = predict_model(args)
        
        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()