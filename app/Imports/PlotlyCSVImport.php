<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PlotlyCSVImport implements ToCollection, WithHeadingRow, WithStartRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        // Process the data as per your requirements
        //foreach ($rows as $row) {
            // Access individual columns using the column headers
            //$column1 = $row['column1'];
            //$column2 = $row['column2'];

            // Perform actions on the columns as needed
        //}
    }

    public function startRow(): int 
    {
         return 1;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
