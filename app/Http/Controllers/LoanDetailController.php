<?php

namespace App\Http\Controllers;

use App\Models\LoanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LoanDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loanDetails = LoanDetail::all();
        return view('loan_detail.index', compact('loanDetails'));
    }

    /**
     * Process data
     */
    public function process_data()
    {
        return view('loan_detail.process_data');
    }

    /**
     * EMI Details
     */
    public function emi_detail()
    {
        $minFirstPaymentDate = $maxLastPaymentDate = '';
        $loanDatesArr = [];
        // Get Min and Max date from loan details
        $dates = DB::table('loan_details')
                    ->select(DB::raw('MIN(first_payment_date) as min_first_payment_date, MAX(last_payment_date) as max_last_payment_date'))
                    ->first();

        if( !empty($dates) ){
            $minFirstPaymentDate = $dates->min_first_payment_date;
            $maxLastPaymentDate = $dates->max_last_payment_date;

            $loanDatesArr = $this->generateLoanMonths($minFirstPaymentDate, $maxLastPaymentDate);
            if( $loanDatesArr ){
                $createTableCommand = $this->createEmiDetailsTableCommand($loanDatesArr);
                DB::statement('DROP TABLE IF EXISTS emi_details');
                DB::statement($createTableCommand);

                $this->calculate_loan_emi();
            }
        }
       
        $emi_details_data = DB::table('emi_details')
                    ->select('*')->orderby('clientid')->get()->map(function ($item) {
                        return (array) $item;
                    })->toArray();
        $emi_details_keys = array_keys(current($emi_details_data));
        unset($emi_details_keys[array_search("id",$emi_details_keys)]);
        unset($emi_details_keys[array_search("created_at",$emi_details_keys)]);
        return view('loan_detail.emi_details', compact('emi_details_data', 'emi_details_keys'));
    }   // EOF

    private function createEmiDetailsTableCommand($monthYearArray)
    {
        // Start with the base command and columns
        $baseCommand = 'CREATE TABLE emi_details (
                            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            clientid INT UNSIGNED NOT NULL,';
        
        // Generate the columns for each month-year in the array
        $columns = '';
        foreach ($monthYearArray as $monthYear) {
            $columns .= " `$monthYear` VARCHAR(50) NOT NULL DEFAULT '0.00',";
        }
        $columns = rtrim($columns, ',');
        
        // Combine the columns with the base command
        $createTableCommand = $baseCommand . $columns . ');';
        
        // Return the complete SQL command
        return $createTableCommand;
    }

    private function generateLoanMonths($minFirstPaymentDate, $maxLastPaymentDate)
    {
        $start = \Carbon\Carbon::createFromFormat('Y-m-d', $minFirstPaymentDate)->startOfMonth();
        $end = \Carbon\Carbon::createFromFormat('Y-m-d', $maxLastPaymentDate)->startOfMonth();

        $datesArray = [];

        while ($start <= $end) {
            $datesArray[] = $start->format('Y_M');
            $start->addMonth();
        }

        return $datesArray;
    }   // EOF

    private function calculate_loan_emi(){
        $loanDetails = LoanDetail::all();

        // $insertArr = [];
        if( !empty($loanDetails) ){
            foreach($loanDetails as $loan){
                $loanFirstDate = $loan->first_payment_date;
                $loanLastDate = $loan->last_payment_date;
                $clientID = $loan->clientid;
                $loanTenure = $loan->num_of_payment;
                $loanAmount = $loan->loan_amount;
                $EmiAmount = round(($loanAmount/$loanTenure), 2);
                $totalLoanAmount = $EmiAmount * $loanTenure;
               
                $preciseAmount = round(($totalLoanAmount - $loanAmount), 2);
                $lastEmi = $EmiAmount - $preciseAmount;

                $loanDatesArr = $this->generateLoanMonths($loanFirstDate, $loanLastDate);
                
                $temp = [];
                $temp['clientid'] = $clientID;
                
                $tenureCount = 1;
                if($loanDatesArr){
                    foreach($loanDatesArr as $colName){
                        $finalEmiAmount = $EmiAmount;
                        
                        if( ($tenureCount == $loanTenure) && ( $preciseAmount != 0 )){
                            $finalEmiAmount = $lastEmi. ' (Adjusted)';
                        }
                        
                        $temp[$colName] = $finalEmiAmount;
                        $tenureCount++;
                    }
                }
                
                // $insertArr[] = $temp;
                // echo "<pre>";
                // echo "clientID --> ", $clientID, "<br>";
                // echo "loanTenure --> ", $loanTenure, "<br>";
                // echo "loanAmount --> ", $loanAmount, "<br>";
                // echo "EmiAmount --> ", $EmiAmount, "<br>";
                // echo "totalLoanAmount --> ", $totalLoanAmount, "<br>";
                // echo "preciseAmount --> ", $preciseAmount, "<br>";
                // echo "lastEmi --> ", $lastEmi, "<br>";
                // echo "<br>";
                // echo "<pre>";
                DB::table('emi_details')->insert($temp);
            }


        }

    }   // EOF

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanDetail $loanDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanDetail $loanDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanDetail $loanDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanDetail $loanDetail)
    {
        //
    }
}
