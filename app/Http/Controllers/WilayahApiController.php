<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah;

class WilayahApiController extends Controller
{
    protected $colKode = 'kode';
    protected $colNama = 'nama';

    public function getProvinces()
    {
        $provinces = Wilayah::whereRaw('CHAR_LENGTH(' . $this->colKode . ') = 2')
            ->orderBy($this->colNama)
            ->get();
        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $provinceId = $request->query('province_id');
        if (!$provinceId) {
            return response()->json([]);
        }
        $cities = Wilayah::whereRaw('CHAR_LENGTH(' . $this->colKode . ') = 5')
            ->where($this->colKode, 'like', $provinceId . '.%')
            ->orderBy($this->colNama)
            ->get();
        return response()->json($cities);
    }

    public function getDistricts(Request $request)
    {
        $cityId = $request->query('city_id');
        if (!$cityId) {
            return response()->json([]);
        }
        $districts = Wilayah::whereRaw('CHAR_LENGTH(' . $this->colKode . ') = 8')
            ->where($this->colKode, 'like', $cityId . '.%')
            ->orderBy($this->colNama)
            ->get();
        return response()->json($districts);
    }

    public function getVillages(Request $request)
    {
        $districtId = $request->query('district_id');
        if (!$districtId) {
            return response()->json([]);
        }
        $villages = Wilayah::whereRaw('CHAR_LENGTH(' . $this->colKode . ') = 13')
            ->where($this->colKode, 'like', $districtId . '.%')
            ->orderBy($this->colNama)
            ->get();
        return response()->json($villages);
    }
}
