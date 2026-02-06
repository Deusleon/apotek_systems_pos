<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;

class DeliveryNoteController extends Controller
{
    //index function to show delivery note page
    public function index()
    {
        //$enableReceiptPrinting = Setting::where('id', 117)->value('value') ?? 'YES';
        return view('sales.delivery_notes.index');
    }

}
