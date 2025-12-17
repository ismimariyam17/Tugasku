namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::all();
        return view('dashboard', compact('datasets'));
    }
    public function store(Request $request)
    {
        $request->validate(['file_csv' => 'required|mimes:csv,txt']);
        $path = $request->file('file_csv')->store('public/datasets');
        $file = fopen(storage_path('app/' . $path), 'r');
        $header = fgetcsv($file); 
        
        if ($header === false) {
            fclose($file);
            return back()->withErrors(['file_csv' => 'File CSV kosong atau tidak valid.']);
        }

        $rowCount = 0;
        while (fgetcsv($file)) { $rowCount++; } 
        fclose($file);

        Dataset::create([
            'name' => $request->file('file_csv')->getClientOriginalName(),
            'file_path' => $path,
            'total_rows' => $rowCount,
            'total_columns' => count($header),
        ]);

        return back()->with('success', 'Dataset berhasil diupload!');
    }
}