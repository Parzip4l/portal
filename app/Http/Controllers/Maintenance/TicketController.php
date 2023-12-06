<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Maintenance\TicketModel;
use App\Slack;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
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

        $ticketdata = TicketModel::all();
        return view('pages.maintenance.tiket.index', compact('ticketdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.maintenance.tiket.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    function generateRandomCode($length = 6) {
        $characters = '0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function store(Request $request)
    {
        try {
            $randomCode = $this->generateRandomCode();
            // Simpan data pembelian
            $tiket = new TicketModel(); 
            $tiket->nomor = $randomCode;
            $tiket->tanggal = $request->tanggal;
            $tiket->permasalahan = $request->permasalahan;
            $tiket->lokasi = $request->lokasi;
            $tiket->pengirim = $request->pengirim;
            $tiket->assign_to = $request->assign_to;
            $tiket->status = 'Menunggu';
            $tiket->kategori = $request->kategori;
            $tiket->save();

            $slackChannel = Slack::where('channel', 'Maintenance Update')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Tiket Maintenance Dengan Nomor Tiket {$randomCode}",
                'attachments' => [
                    [
                        'title' => '',
                        'fields' => [
                            [
                                'title' => 'Tanggal',
                                'value' => $request->tanggal,
                                'short' => true,
                            ],
                            [
                                'title' => 'Permasalahan',
                                'value' => $request->permasalahan,
                                'short' => true,
                            ],
                            [
                                'title' => 'Urgensi',
                                'value' => $request->kategori,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/maintenance-ticket)',
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
        try {
            // Lakukan validasi input sesuai kebutuhan
            $this->validate($request, [
                'status' => 'required|in:Menunggu,Dikerjakan,Selesai',
                'estimasi_waktu' => 'required',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            ]);
    
            // Temukan tiket berdasarkan ID
            $maintenanceTicket = TicketModel::findOrFail($id);
    
            // Update data tiket
            $maintenanceTicket->status = $request->status;
            $maintenanceTicket->estimasi_waktu = $request->estimasi_waktu;
            $maintenanceTicket->tanggal_mulai = $request->tanggal_mulai;
            $maintenanceTicket->tanggal_selesai = $request->tanggal_selesai;
    
            // Simpan perubahan
            $maintenanceTicket->save();

            $slackChannel = Slack::where('channel', 'Maintenance Update')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Update Tiket Maintenance Dengan Nomor Tiket {$maintenanceTicket->nomor}",
                'attachments' => [
                    [
                        'title' => '',
                        'fields' => [
                            [
                                'title' => 'Tanggal',
                                'value' => $request->tanggal,
                                'short' => true,
                            ],
                            [
                                'title' => 'Permasalahan',
                                'value' => $request->permasalahan,
                                'short' => true,
                            ],
                            [
                                'title' => 'Status',
                                'value' => $request->status,
                                'short' => true,
                            ],
                            [
                                'title' => 'Estimasi Pengerjaan',
                                'value' => $request->estimasi_waktu,
                                'short' => true,
                            ],
                            [
                                'title' => 'Tanggal Mulai',
                                'value' => $request->tanggal_mulai,
                                'short' => true,
                            ],
                            [
                                'title' => 'Tanggal Selesai',
                                'value' => $request->tanggal_selesai,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/maintenance-ticket)',
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
    
            return redirect()->route('maintenance-ticket.index')->with('success', 'Data tiket berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data tiket: ' . $e->getMessage());
        }
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
