<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CodeGeneratorController extends Controller
{
    public function index()
    {
        return view('code_generator');
    }

    public function generate(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);
        $prompt = strtolower($request->prompt);

        // 1. ANALISIS ENTITAS
        $schema = $this->analyzeEntity($prompt);

        // 2. DETEKSI BAHASA
        $targetLanguages = $this->detectTargetLanguages($prompt);

        // 3. GENERATE KODE
        $finalOutput = "";
        
        foreach ($targetLanguages as $lang) {
            $finalOutput .= "/* ==========================================\n";
            $finalOutput .= " * GENERATED {$lang} APP FOR: {$schema['name']}\n";
            $finalOutput .= " * FEATURES: Create, Read, Update, Delete, Search\n";
            $finalOutput .= " * ========================================== */\n\n";
            
            $finalOutput .= $this->generateCodeForLanguage($lang, $schema);
            $finalOutput .= "\n\n" . str_repeat("=", 50) . "\n\n";
        }

        return back()->with([
            'generated_code' => $finalOutput,
            'framework_used' => implode(' + ', $targetLanguages),
            'prompt_used' => $request->prompt
        ]);
    }

    // =========================================================================
    // 1. OTAK PEMAHAMAN ENTITAS (ENTITY BRAIN)
    // =========================================================================
    private function analyzeEntity($prompt)
    {
        $entity = [
            'name' => 'Item',
            'var' => 'item',
            'primary_key' => 'id',
            'fields' => ['id' => 'integer', 'name' => 'string', 'description' => 'string']
        ];

        if (str_contains($prompt, 'mahasiswa') || str_contains($prompt, 'mhs') || str_contains($prompt, 'student')) {
            return [
                'name' => 'Mahasiswa',
                'var' => 'mhs',
                'primary_key' => 'nim',
                'fields' => [
                    'nim' => 'string',
                    'nama_lengkap' => 'string',
                    'jurusan' => 'string',
                    'semester' => 'integer',
                    'ipk' => 'float'
                ]
            ];
        }

        if (str_contains($prompt, 'dosen') || str_contains($prompt, 'guru')) {
            return [
                'name' => 'Dosen',
                'var' => 'dosen',
                'primary_key' => 'nidn',
                'fields' => [
                    'nidn' => 'string',
                    'nama_dosen' => 'string',
                    'matkul_utama' => 'string',
                    'gaji' => 'integer'
                ]
            ];
        }

        if (str_contains($prompt, 'produk') || str_contains($prompt, 'barang')) {
            return [
                'name' => 'Produk',
                'var' => 'produk',
                'primary_key' => 'kode_barang',
                'fields' => [
                    'kode_barang' => 'string',
                    'nama_barang' => 'string',
                    'kategori' => 'string',
                    'harga' => 'integer',
                    'stok' => 'integer'
                ]
            ];
        }

        return $entity;
    }

    // =========================================================================
    // 2. DETEKSI MULTI-BAHASA
    // =========================================================================
    private function detectTargetLanguages($prompt)
    {
        $languages = [];
        if (str_contains($prompt, 'python') || str_contains($prompt, 'py')) $languages[] = 'Python';
        if (str_contains($prompt, 'laravel') || str_contains($prompt, 'php')) $languages[] = 'Laravel';
        if (str_contains($prompt, 'c++') || str_contains($prompt, 'cpp')) $languages[] = 'C++';
        
        if (empty($languages)) return ['Python']; // Default
        return $languages;
    }

    private function generateCodeForLanguage($lang, $schema)
    {
        switch ($lang) {
            case 'C++': return $this->generateCPPConsoleApp($schema);
            case 'Laravel': return $this->generateLaravelCRUD($schema);
            case 'Python': return $this->generatePythonCRUD($schema);
            default: return "// No template for $lang";
        }
    }

    // =========================================================================
    // 3. GENERATOR C++ (FIXED)
    // =========================================================================
    private function generateCPPConsoleApp($schema)
    {
        $className = $schema['name'];
        $pk = $schema['primary_key'];
        
        $structMembers = "";
        $constructorParams = "";
        $constructorAssign = "";
        $displayLogic = "";
        $inputLogic = "";
        $updateLogic = "";

        $i = 0;
        foreach ($schema['fields'] as $field => $type) {
            $cppType = match($type) {
                'integer' => 'int',
                'float' => 'float',
                'decimal' => 'double',
                default => 'string'
            };

            $structMembers .= "    $cppType $field;\n";
            
            $comma = ($i < count($schema['fields']) - 1) ? ", " : "";
            $constructorParams .= "$cppType p_$field$comma";
            $constructorAssign .= "        $field = p_$field;\n";

            $displayLogic .= "        cout << \"$field: \" << $field << \" | \";\n";

            // INPUT LOGIC
            $inputLogic .= "        cout << \"Masukkan $field: \";\n";
            if ($cppType == 'string') {
                // Khusus input string, pakai getline (kecuali PK biar ga ribet buffer)
                if ($field == $pk) {
                    $inputLogic .= "        cin >> $field;\n"; 
                } else {
                    $inputLogic .= "        if (cin.peek() == '\\n') cin.ignore(); getline(cin, $field);\n";
                }
            } else {
                $inputLogic .= "        cin >> $field;\n";
            }

            // UPDATE LOGIC (FIXED ERROR HERE)
            if ($field != $pk) {
                $updateLogic .= "                cout << \"Masukkan $field Baru: \";\n";
                if ($cppType == 'string') {
                    // Perhatikan tanda backslash (\) sebelum -> agar tidak dibaca PHP
                    $updateLogic .= "                if (cin.peek() == '\\n') cin.ignore(); getline(cin, it->$field);\n";
                } else {
                    $updateLogic .= "                cin >> it->$field;\n";
                }
            }
            $i++;
        }

        return <<<EOT
#include <iostream>
#include <vector>
#include <string>
#include <algorithm>
#include <iomanip>

using namespace std;

class $className {
public:
$structMembers

    $className() {} 
    
    $className($constructorParams) {
$constructorAssign
    }

    auto getId() const { return $pk; }

    void display() const {
        cout << "- ";
$displayLogic
        cout << endl;
    }
};

class {$className}Manager {
private:
    vector<$className> records;

public:
    void create() {
        $structMembers
        
        cout << "\\n--- TAMBAH DATA $className ---" << endl;
$inputLogic
        
        // Check Duplicate
        for(const auto& r : records) {
            if(r.getId() == $pk) {
                cout << "[ERROR] ID sudah ada!" << endl;
                return;
            }
        }

        records.push_back($className($constructorParams));
        cout << "[SUCCESS] Data berhasil ditambahkan." << endl;
    }

    void readAll() {
        cout << "\\n--- DAFTAR DATA $className ---" << endl;
        if (records.empty()) {
            cout << "(Data Kosong)" << endl;
            return;
        }
        for (const auto& item : records) {
            item.display();
        }
        cout << "Total: " << records.size() << " data." << endl;
    }

    void update() {
        cout << "\\n--- UPDATE DATA ---" << endl;
        cout << "Masukkan ID yang akan diubah: ";
        string targetId; // Asumsi ID string agar aman
        cin >> targetId;

        bool found = false;
        for (auto it = records.begin(); it != records.end(); ++it) {
            // Konversi ID ke string untuk perbandingan aman
            if (to_string(it->getId()) == targetId || (string)it->getId() == targetId) {
$updateLogic
                cout << "[SUCCESS] Data berhasil diupdate." << endl;
                found = true;
                break;
            }
        }
        if (!found) cout << "[ERROR] Data tidak ditemukan." << endl;
    }

    void remove() {
        cout << "\\n--- HAPUS DATA ---" << endl;
        cout << "Masukkan ID yang akan dihapus: ";
        string targetId;
        cin >> targetId;

        int initialSize = records.size();
        // Hapus manual loop untuk kompatibilitas C++ standar
        for (int i = 0; i < records.size(); i++) {
             // Hacky compare for template simplicity
             if (to_string(records[i].getId()) == targetId || (string)records[i].getId() == targetId) {
                 records.erase(records.begin() + i);
                 cout << "[SUCCESS] Data berhasil dihapus." << endl;
                 return;
             }
        }
        cout << "[ERROR] Data tidak ditemukan." << endl;
    }
};

int main() {
    {$className}Manager app;
    int choice;

    do {
        cout << "\\n=== APLIKASI CRUD " . strtoupper("$className") << " ===" << endl;
        cout << "1. Tambah Data" << endl;
        cout << "2. Tampilkan Semua" << endl;
        cout << "3. Update Data" << endl;
        cout << "4. Hapus Data" << endl;
        cout << "5. Keluar" << endl;
        cout << "Pilihan: ";
        cin >> choice;

        switch (choice) {
            case 1: app.create(); break;
            case 2: app.readAll(); break;
            case 3: app.update(); break;
            case 4: app.remove(); break;
            case 5: cout << "Terima kasih!" << endl; break;
            default: cout << "Pilihan tidak valid." << endl;
        }
    } while (choice != 5);

    return 0;
}
EOT;
    }

    // =========================================================================
    // 4. GENERATOR PYTHON (CLI APP)
    // =========================================================================
    private function generatePythonCRUD($schema)
    {
        $className = $schema['name'];
        $initParams = implode(", ", array_keys($schema['fields']));
        $selfAssign = "";
        $inputLogic = "";
        
        foreach ($schema['fields'] as $field => $type) {
            $selfAssign .= "        self.$field = $field\n";
            $inputType = ($type == 'integer' || $type == 'float') ? 'float' : 'str';
            $inputLogic .= "        $field = $inputType(input('Masukkan $field: '))\n";
        }

        return <<<EOT
import os
import sys

class $className:
    def __init__(self, $initParams):
$selfAssign

    def __str__(self):
        return str(self.__dict__)

class App:
    def __init__(self):
        self.db = []

    def create(self):
        print("\\n--- TAMBAH DATA ---")
$inputLogic
        new_obj = $className($initParams)
        self.db.append(new_obj)
        print("Data berhasil disimpan!")

    def read(self):
        print(f"\\n--- DAFTAR {strtoupper($className)} ---")
        if not self.db:
            print("Data kosong.")
        for i, item in enumerate(self.db):
            print(f"{i+1}. {item}")

    def delete(self):
        self.read()
        try:
            idx = int(input("Hapus nomor urut ke: ")) - 1
            if 0 <= idx < len(self.db):
                del self.db[idx]
                print("Data dihapus.")
            else:
                print("ID tidak valid.")
        except:
            print("Error input.")

    def run(self):
        while True:
            print("\\n1. Tambah  2. Lihat  3. Hapus  4. Keluar")
            ch = input("Pilih: ")
            if ch == '1': self.create()
            elif ch == '2': self.read()
            elif ch == '3': self.delete()
            elif ch == '4': break

if __name__ == "__main__":
    app = App()
    app.run()
EOT;
    }

    // =========================================================================
    // 5. GENERATOR LARAVEL (CONTROLLER)
    // =========================================================================
    private function generateLaravelCRUD($schema)
    {
        $className = $schema['name'];
        $varName = strtolower($schema['name']);
        
        $validationRules = "";
        foreach ($schema['fields'] as $field => $type) {
            $rule = ($type == 'integer' || $type == 'float') ? 'numeric' : 'string|max:255';
            if ($field == $schema['primary_key']) $rule .= "|unique:".Str::plural($varName);
            $validationRules .= "            '$field' => 'required|$rule',\n";
        }

        return <<<EOT
<?php

namespace App\Http\Controllers;

use App\Models\\$className;
use Illuminate\Http\Request;

class {$className}Controller extends Controller
{
    // MENAMPILKAN SEMUA DATA
    public function index()
    {
        \$$varName = $className::latest()->paginate(10);
        return view('{$varName}.index', compact('$varName'));
    }

    // MENYIMPAN DATA BARU
    public function store(Request \$request)
    {
        \$validated = \$request->validate([
$validationRules
        ]);

        $className::create(\$validated);

        return redirect()->route('{$varName}.index')
            ->with('success', 'Data $className berhasil ditambahkan');
    }

    // UPDATE DATA
    public function update(Request \$request, $className \$$varName)
    {
        \$validated = \$request->validate([
$validationRules
        ]);

        \$$varName->update(\$validated);

        return redirect()->route('{$varName}.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    // HAPUS DATA
    public function destroy($className \$$varName)
    {
        \$$varName->delete();
        return redirect()->route('{$varName}.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
EOT;
    }
}