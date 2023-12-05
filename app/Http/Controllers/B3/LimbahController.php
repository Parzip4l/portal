<?php

namespace App\Http\Controllers\B3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\B3\LimbahModel;
use App\Slack;
use Illuminate\Support\Facades\Auth;

class LimbahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $limbahdata = LimbahModel::all();

        return view('pages.b3.index', compact('limbahdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.b3.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Simpan data pembelian
            $limbahdata = new LimbahModel(); // Generate UUID
            $limbahdata->tanggal = $request->tanggal;
            $limbahdata->jenis = $request->jenis;
            $limbahdata->asal = $request->asal;
            $limbahdata->jumlah = $request->jumlah;
            $limbahdata->save();

            $slackChannel = Slack::where('channel', 'Testing Channel')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Log Book Limbah B3 {$request->tanggal}",
                'attachments' => [
                    [
                        'title' => 'Data Limbah B3',
                        'fields' => [
                            [
                                'title' => 'Tanggal',
                                'value' => $request->tanggal,
                                'short' => true,
                            ],
                            [
                                'title' => 'Jenis',
                                'value' => $request->jenis,
                                'short' => true,
                            ],
                            [
                                'title' => 'Asal Limbah',
                                'value' => $request->asal,
                                'short' => true,
                            ],
                            [
                                'title' => 'Jumlah',
                                'value' => $request->jumlah,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/pencatatan-limbah)',
                                'short' => true,
                            ]
                        ],
                    ],
                ],
                
            ];

            $data_string = json_encode($data);

            $ch = curl_init($slackWebhookUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
            ]);

            $result = curl_exec($ch);

            if ($result === false) {
                // Penanganan kesalahan jika Curl gagal
                $error = curl_error($ch);
                // Handle the error here
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim data ke Slack: ' . $error);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode !== 200) {
                // Penanganan kesalahan jika Slack merespons selain status 200 OK
                // Handle the error here
                return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim data ke Slack. Kode status: ' . $httpCode);
            }

            curl_close($ch);
    
            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            // Tangani kesalahan yang mungkin terjadi
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan Data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
