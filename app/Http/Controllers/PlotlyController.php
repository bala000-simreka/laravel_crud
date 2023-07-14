<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PlotlyCSVImport;

class PlotlyController extends Controller
{
    public function index()
    {
        $plotData = $this->getAllDataCSV();
        $allCols = !empty($plotData) ? $plotData[0] : '';
        return view('plotly.index', compact('allCols'));
    }

    public function visualise()
    {
        return view('plotly.visualise');
    }

    public function listCSVsheetjs()
    {
        return view('plotly.list-csv-sheetjs');
    }

    public function listCSVTable(){
        return view('plotly.list-csv-table');
    }

    //Maatwebsite/excel implementation of csv
    public function importCSV()
    {
        $path = storage_path('app/public/50k-36.csv'); // execution time exceeded 60s
        //$path = storage_path('app/public/BostonHousing_10k.csv'); // execution time - 15.47s
        //$path = storage_path('app/public/BostonHousing.csv'); //execution time - 755ms
        $data = Excel::toCollection(new PlotlyCSVImport(), $path);

        // Return the data to the view
        return view('plotly.list-new', ['allData' => $data[0]]);
    }

    //league/csv implementation of csv
    public function getAllDataCSV(){
        $plotData = [];
        $filePath = Storage::disk('local')->path('public/BostonHousing.csv');;

        $reader = Reader::createFromPath($filePath, 'r');
        //$reader->setHeaderOffset(0); // Skip header row if present

        $limit = 100; // Number of rows to process at a time
        $offset = 0; // Initial offset

        $stmt = (new Statement())->offset($offset)->limit($limit);
        $records = $stmt->process($reader);
    
        while ($records->count() > 0) {
            foreach ($records as $record) {
                // Process each row here
                $plotData[] = $record;
            }
    
            $offset += $limit;
            $stmt = (new Statement())->offset($offset)->limit($limit);
            $records = $stmt->process($reader);
        }

        return $plotData;
    }

    public function listData()
    {
        $plotData = $this->getAllDataCSV();
        $allCols = !empty($plotData) ? $plotData[0] : '';
        array_shift($plotData);

        return view('plotly.list-data', ['allData' => $plotData, 'cols' => $allCols]);
    }

    //No need of this function
    public function getColumnDataCSV(Request $request){
        $columnData = [];
        
        $x_axis = $request->input('x_axis_values') ?? [];
        $y_axis = $request->input('y_axis_values') ?? [];
        $z_axis = $request->input('z_axis_values') ?? [];

        $columnData['x'] = $x_axis;
            $columnData['y'] = $y_axis;
            $columnData['z'] = $z_axis;

        /*if($x_axis !== '' && $y_axis !== '' && $z_axis !== ''){
            $columnData['x'] = $x_axis;
            $columnData['y'] = $y_axis;
            $columnData['z'] = $z_axis;
        } else if($x_axis !== '' && $y_axis !== ''){
            $columnData['x'] = $x_axis;
            $columnData['y'] = $y_axis;
        } else {
            $columnData['x'] = $x_axis;
        }*/
        return $columnData;
    }

       
}
