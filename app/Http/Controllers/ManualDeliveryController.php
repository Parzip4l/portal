<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ManualDelivery;
use Carbon\Carbon;
use App\Slack;

class ManualDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ManualDelivery::orderBy('created_at', 'desc')->get();
        $data2 = ManualDelivery::orderBy('created_at', 'desc')->get();
        return view('pages.delivery.manual', compact('data','data2'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function updateStatus(Request $request)
    {
        
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

            $tanggalOrder = Carbon::parse($request->tanggal_order);
            $targetKirim = $tanggalOrder->addDays(7)->toDateString();
            // Simpan data pembelian
            $purchase = new ManualDelivery(); // Generate UUID
            $purchase->tanggal_order = $request->tanggal_order;
            $purchase->target_kirim = $targetKirim;
            $purchase->tanggal_kirim = $request->tanggal_kirim;
            $purchase->customer = $request->customer;
            $purchase->driver = $request->driver;
            $purchase->status = $request->status;
            $purchase->nomor_so = $request->nomor_so;
            $purchase->ekspedisi = $request->ekspedisi;
            $purchase->nama_barang = $request->nama_barang;
            $purchase->total_order = $request->total_order;
            $purchase->sisa_order = $request->sisa_order;
            $purchase->keterangan = $request->keterangan;
            $purchase->save();

            $slackChannel = Slack::where('channel', 'Jadwal Pengiriman')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Orderan {$request->customer} Pada Tanggal {$request->tanggal_order}",
                'attachments' => [
                    [
                        'title' => 'Data Orderan',
                        'fields' => [
                            [
                                'title' => 'Nomor SO',
                                'value' => $request->nomor_so,
                                'short' => true,
                            ],
                            [
                                'title' => 'Nama Barang',
                                'value' => $request->nama_barang,
                                'short' => true,
                            ],
                            [
                                'title' => 'Total Order',
                                'value' => $request->total_order,
                                'short' => true,
                            ],
                            [
                                'title' => 'Status Pengiriman',
                                'value' => $request->status,
                                'short' => true,
                            ],
                            [
                                'title' => 'Keterangan',
                                'value' => $request->keterangan,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/manual-delivery)',
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
    
            return redirect()->route('manual-delivery.index')->with('success', 'Data berhasil disimpan.');
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
        $data = ManualDelivery::find($id);

        return view('pages.delivery.edit', compact('data'));
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
        $delivery = ManualDelivery::find($id);
        $status = $request->input('status');
        if (!$delivery) {
            return response()->json(['error' => 'Delivery not found.'], 404);
        }

        // Update the status
        $delivery->status = $status;
        $delivery->save();

        $slackChannel = Slack::where('channel', 'Jadwal Pengiriman')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Orderan {$delivery->customer} Pada Tanggal {$delivery->tanggal_order}",
                'attachments' => [
                    [
                        'title' => 'Data Orderan ' . $delivery->customer,
                        'fields' => [
                            [
                                'title' => 'Nomor SO',
                                'value' => $delivery->nomor_so,
                                'short' => true,
                            ],
                            [
                                'title' => 'Nama Barang',
                                'value' => $delivery->nama_barang,
                                'short' => true,
                            ],
                            [
                                'title' => 'Total Order',
                                'value' => $delivery->total_order,
                                'short' => true,
                            ],
                            [
                                'title' => 'Status Pengiriman',
                                'value' => $status,
                                'short' => true,
                            ],
                            [
                                'title' => 'Keterangan',
                                'value' => $delivery->keterangan,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/manual-delivery)',
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

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function UpdateSeluruhData(Request $request, $id)
    {
        $delivery = ManualDelivery::find($id);
        $tanggal_order = $request->input('tanggal_order');
        $tanggal_kirim = $request->input('tanggal_kirim');
        $customer = $request->input('customer');
        $nomor_so = $request->input('nomor_so');
        $ekspedisi = $request->input('ekspedisi');
        $nama_barang = $request->input('nama_barang');
        $total_order = $request->input('total_order');
        $sisa_order = $request->input('sisa_order');
        $driver = $request->input('driver');
        $keterangan = $request->input('keterangan');
        $status = $request->input('status');
        if (!$delivery) {
            return response()->json(['error' => 'Delivery not found.'], 404);
        }

        // Update the status
        $delivery->tanggal_order = $tanggal_order;
        $delivery->tanggal_kirim = $tanggal_kirim;
        $delivery->customer = $customer;
        $delivery->nomor_so = $nomor_so;
        $delivery->customer = $customer;
        $delivery->ekspedisi = $ekspedisi;
        $delivery->nama_barang = $nama_barang;
        $delivery->total_order = $total_order;
        $delivery->sisa_order = $sisa_order;
        $delivery->driver = $driver;
        $delivery->keterangan = $keterangan;
        $delivery->status = $status;
        $delivery->save();

        $slackChannel = Slack::where('channel', 'Jadwal Pengiriman')->first();
            $slackWebhookUrl = $slackChannel->url;
            $today = now()->toDateString();
            $data = [
                'text' => "Orderan {$delivery->customer} Pada Tanggal {$delivery->tanggal_order}",
                'attachments' => [
                    [
                        'title' => 'Data Orderan ' . $delivery->customer,
                        'fields' => [
                            [
                                'title' => 'Nomor SO',
                                'value' => $delivery->nomor_so,
                                'short' => true,
                            ],
                            [
                                'title' => 'Nama Barang',
                                'value' => $delivery->nama_barang,
                                'short' => true,
                            ],
                            [
                                'title' => 'Total Order',
                                'value' => $delivery->total_order,
                                'short' => true,
                            ],
                            [
                                'title' => 'Status Pengiriman',
                                'value' => $status,
                                'short' => true,
                            ],
                            [
                                'title' => 'Keterangan',
                                'value' => $delivery->keterangan,
                                'short' => true,
                            ],
                            [
                                'title' => 'Lihat Detail Data Di Champoil Portal',
                                'value' => '(https://portal.champoil.co.id/manual-delivery)',
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

        return redirect()->back()->with('success', 'Delivery Data Updated successfully.');
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
