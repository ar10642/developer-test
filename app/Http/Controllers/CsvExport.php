<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class CsvExport extends Controller {
	/**
	 * Converts the user input into a CSV file and streams the file back to the user
	 */
	public function convert(Request $request)
	{

		$headers = [
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=CsvExport.csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0"
		];

		$export = function() use($request) {
			$handle = fopen('php://output', 'w');
			foreach($request->input() as $row) {
				fputcsv($handle, $row);
			}
			fclose($handle);
		};

		return response()->stream($export, 200, $headers);

	}
}
