<?php

namespace App\Http\Controllers;


class PdfController extends Controller
{


    public static function pdfVersion($musicians, $instruments_filter) {

        $pdf = '<html lang="en"><head><title>Musicians DB</title><style>table,tr,td{border:1px solid;}table{width:100%;border-collapse:collapse;}</style></head><body>';

        $pdf .= '<h1 style="font-size:300%">Musicians DB</h1>';
        $pdf .= '<br><br>';
        $pdf .= 'Instruments: ';
        if ($instruments_filter) {
            foreach ($instruments_filter as $instrument) $pdf .= $instrument . ', ';
            $pdf = rtrim($pdf, ', ');
        }
        else $pdf .= 'all';
        $pdf .= '<br>';
        if (request('name')) $pdf .= 'Search for: "' . request('name') . '"';
        $pdf .= '<br><br>';
        $pdf .= '<table style="width:100%">';
        $pdf .= '<tr>';
        $pdf .= '<th>Name</th>';
        $pdf .= '<th>Instruments Played</th>';
        $pdf .= '<th>Contact details</th>';
        $pdf .= '</tr>';

        foreach ($musicians as $musician) {
            $pdf .= '<tr>';
            $pdf .= '<td style="padding:12px;">';
            $pdf .= $musician->first_name . ' ' . $musician->last_name;
            $pdf .= '</td>';

            $pdf .= '<td style="padding:12px;">';
            foreach ($musician->instruments as $instrument) {
                $pdf .= $instrument->name;
                $pdf .= ', ';
            }
            $pdf = rtrim($pdf, ', ');
            $pdf .= '</td>';
            $pdf .= '<td style="padding:12px;">';
            for ($i = 0; $i < count($musician->musician_details_text); $i++) {

                $pdf .= $musician->detail_types[$i] . ': ';
                $pdf .=  '<span style="font-size:80%">' . $musician->musician_details_text[$i] . '</span>';
                $pdf .=  '<br>';

            }
            $pdf .= '</td>';
            $pdf .= '</tr>';

        }


        $pdf .= '</table>';
        $pdf .= '</body></html>';

        return $pdf;
    }



}
