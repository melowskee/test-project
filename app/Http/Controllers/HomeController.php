<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SenderEmail;
use App\Campaign;
use App\Contact;
use App\Deliveries;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $campaign = Campaign::find(1);
        return view('home')->with(['campaign' => $campaign]);
    }

    public function parseCSV() {
        if (isset($_FILES['csv'])) {
            $csv_file = $_FILES['csv']['tmp_name'];
            $delimiter = (isset($_POST['delimiter']) && $_POST['delimiter']) ? $_POST['delimiter'] : ',';
    
            $fh = fopen($csv_file, 'r');
            $column_names = array_map("trim", fgetcsv($fh, 0, $delimiter));
            $data = array();
            while (($csv_line = fgetcsv($fh, 0, $delimiter)) !== false) {
                $data[] = array_map("trim", $csv_line);
            }
            fclose($fh);
    
            if (count($data) < 1) {
                echo json_encode(array('error' => 'The csv file should at least contain 2 lines (column names + data line)'));
                exit();
            }
    
            echo json_encode(array( 'column_names' => $column_names,
                                    'data' => $data));
        } else {
            echo json_encode(array('error' => 'Please upload a csv file'));
        }
    }

    public function  sendEmail(Request $request) {
        //dd($request->all());
        if (!$request->has('from_name') || $request->get('from_name') =='') {
            echo json_encode(array('error' => 'The sender name is required.'));
            return;
        }
        if (!$request->has('from_address') || $request->get('from_address') =='') {
            echo json_encode(array('error' => 'The sender email is required.'));
            return;
        }
        if (!$request->has('recipient') || $request->get('recipient') =='') {
            echo json_encode(array('error' => 'The recipient email is required.'));
            return;
        }
        if (!$request->has('subject') || $request->get('subject') =='') {
            echo json_encode(array('error' => 'The subject is required.'));
            return;
        }
        if (!$request->has('body') || $request->get('body') =='') {
            echo json_encode(array('error' => 'The email body is required.'));
            return;
        }

        $data = array(
            'subject' =>  $request->get('subject'),
            'body' =>  $request->get('body')
        );

        // send emails
        Mail::to($request->get('from_address'), $request->get('recipient'))->send(new SenderEmail($data));
        
        // create contacts
        $existing = Contact::whereEmail($request->get('recipient'))->first();
       
        if($existing){
             // if contacts exist create delivery record
            $delivery = new Deliveries;
            $delivery->contact_id = $existing->id;
            $delivery->content =  $request->get('body');
            $delivery->save();
        }else{
            $columns = json_decode($request->get('information'))->column_names;
            $data = json_decode($request->get('information'))->data[$request->get('count')];
            $information = array();
            $information['columns'] = $columns;
            $information['data'] = $data;
            // unset($data[1]);
            // unset($data[2]);
            // unset($data[3]);
            // dd($data);
            // create new contacts 
            $contact = new Contact;
            $contact->email = $request->get('recipient');
            $contact->information = json_encode($information);
            $contact->save();
            // create delivery record
            $delivery = new Deliveries;
            $delivery->contact_id = $contact->id;
            $delivery->content =  $request->get('body');
            $delivery->save();
        }    
        
        echo json_encode(array());
    }
}
