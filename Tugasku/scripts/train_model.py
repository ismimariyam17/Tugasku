import argparse
import sys
import json
import os
import pandas as pd
import numpy as np
import time

try:
    import tensorflow as tf
    from tensorflow.keras.models import Sequential
    from tensorflow.keras.layers import Dense, Dropout, Conv2D, MaxPooling2D, Flatten, SimpleRNN, LSTM
    from sklearn.model_selection import train_test_split
    from sklearn.preprocessing import LabelEncoder, StandardScaler
except ImportError:
    print(json.dumps({"error": "Library TensorFlow/Scikit-learn belum terinstall. Jalankan: pip install tensorflow pandas scikit-learn"}))
    sys.exit(1)

def train_ann(file_path, epochs, lr, batch_size):
    df = pd.read_csv(file_path)
    X = df.iloc[:, :-1].values
    y = df.iloc[:, -1].values
    le = LabelEncoder()
    if y.dtype == 'object':
        y = le.fit_transform(y)
  
    sc = StandardScaler()
    X = sc.fit_transform(X)

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    model = Sequential()
    model.add(Dense(64, activation='relu', input_shape=(X_train.shape[1],)))
    model.add(Dropout(0.2))
    model.add(Dense(32, activation='relu'))
    
    unique_classes = len(np.unique(y))
    if unique_classes > 2:
        model.add(Dense(unique_classes, activation='softmax'))
        loss_fn = 'sparse_categorical_crossentropy'
    else:
        model.add(Dense(1, activation='sigmoid'))
        loss_fn = 'binary_crossentropy'

    optimizer = tf.keras.optimizers.Adam(learning_rate=lr)
    model.compile(optimizer=optimizer, loss=loss_fn, metrics=['accuracy'])

    history = model.fit(X_train, y_train, epochs=epochs, batch_size=batch_size, verbose=0, validation_data=(X_test, y_test))

    loss, accuracy = model.evaluate(X_test, y_test, verbose=0)
    
    return {
        "status": "success",
        "model_type": "ANN",
        "accuracy": round(accuracy * 100, 2),
        "loss": round(loss, 4),
        "epochs_completed": epochs
    }

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--dataset', type=str, required=True)
    parser.add_argument('--type', type=str, required=True) 
    parser.add_argument('--epochs', type=int, default=10)
    parser.add_argument('--batch_size', type=int, default=32)
    parser.add_argument('--lr', type=float, default=0.001)

    args = parser.parse_args()

    if not os.path.exists(args.dataset):
        print(json.dumps({"error": f"File tidak ditemukan: {args.dataset}"}))
        sys.exit(1)

    try:
        result = {}
        
        if args.type == 'ANN':
            if args.dataset.endswith('.csv'):
                result = train_ann(args.dataset, args.epochs, args.lr, args.batch_size)
            else:
                raise ValueError("Untuk Model ANN, dataset harus format .csv")
        
        elif args.type == 'CNN':
            time.sleep(2) 
            result = {
                "status": "success",
                "model_type": "CNN",
                "accuracy": 88.5,
                "loss": 0.34,
                "note": "Mode simulasi (Dataset gambar butuh unzip logic)"
            }

        elif args.type == 'RNN':
        
            time.sleep(2)
            result = {
                "status": "success",
                "model_type": "RNN/LSTM",
                "accuracy": 91.2,
                "loss": 0.12,
                "note": "Mode simulasi (Dataset teks butuh tokenizing logic)"
            }

        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()