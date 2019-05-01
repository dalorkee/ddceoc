<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class evbController extends Controller
{
	private $str;

	/*
	Display a listing of the resource.
	@return \Illuminate\Http\Response
	*/

	public function idx() {

	}

	/*
	Show the form for creating a new resource.
	@return \Illuminate\Http\Response
	*/
	public function create() {
		//
	}

	/*
	Store a newly created resource in storage.
	@param  \Illuminate\Http\Request  $request
	@return \Illuminate\Http\Response
	*/
	public function store(Request $request) {
		//
	}

	/*
	Display the specified resource.
	@param  int  $id
	@return \Illuminate\Http\Response
	*/
	public function show($id) {
		//
	}

	/*
	Show the form for editing the specified resource.
	@param  int  $id
	@return \Illuminate\Http\Response
	*/
	public function edit($id) {
		//
	}

	/*
	Update the specified resource in storage.
	@param  \Illuminate\Http\Request  $request
	@param  int  $id
	@return \Illuminate\Http\Response
	*/
	public function update(Request $request, $id) {
		//
	}

	/*
	Remove the specified resource from storage.
	@param  int  $id
	@return \Illuminate\Http\Response
	*/
	public function destroy($id) {
		//
	}

	/* Get Events data */
	protected function eventData() {
		$events = DB::table('event_tb')
			->whereNotNull('latitude')
			->orderBy('event_id')
			->get();
		return $events;
	}

	/* count all case */
	protected function countEvents($dRange=0) {
		$dRange = $this->setDateFormat($dRange);
		$cnt = DB::table('event_tb')
			->select(DB::raw('count(*) as total_count'))
			->whereIn('dcir_criterion', [1, 2])
			->whereBetween('event_notifier_date', $dRange)
			->get()
			->toArray();
		return $cnt;
	}

	/* count event by dcir status */
	protected function countDcirCase($dRange=0) {
		$dRange = $this->setDateFormat($dRange);
		$cnt = DB::table('event_tb')
			->select(DB::raw('count(*) as dcir_case'))
			->where('dcir_criterion', 1)
			->where('dcir_status', 1)
			->whereBetween('event_notifier_date', $dRange)
			->get()
			->toArray();
		return $cnt;
	}

	protected function countNonDcirCase() {
		$cnt = DB::table('event_tb')
			->select(DB::raw('count(*) as non_dcir_case'))
			->where('dcir_criterion', 2)
			->where('dcir_status', 1)
			->get()
			->toArray();
		return $cnt;
	}

	protected function countDcirFinishedCase() {
		$cnt = DB::table('event_tb')
			->select(DB::raw('count(*) as dcir_finished'))
			->whereIn('dcir_criterion', [1, 2])
			->where('dcir_status', 2)
			->get()
			->toArray();
		return $cnt;
	}

	/* Get event from dcir status */
	protected function dcirData($dRange=0) {
		$dRange = $this->setDateFormat($dRange);
		$events = DB::table('event_tb')
			->whereNotNull('latitude')
			->whereNotNull('longitude')
			->whereIn('dcir_criterion', [1, 2])
			->where('dcir_status', 1)
			->whereBetween('event_notifier_date', $dRange)
			->orderBy('event_id')
			->get();
		return $events;
	}

	protected function provinceList() {
		$prov = DB::table('province_ref')
			->orderBy('province_id')
			->get();
		return $prov;
	}

	protected function provinceByCode($provCode=array(0)) {
		$prov = DB::table('province_ref')
			->whereIn('province_id', $provCode)
			->get();
		return $prov;
	}

	protected function districtByCode($districtCode=array(0)) {
		$district = DB::table('amphur_ref')
			->whereIn('amphur_id', $districtCode)
			->get();
		return $district;
	}

	protected function subDistrictByCode($subDistrictCode=array(0)) {
		$subDistrict = DB::table('tambol_ref')
			->whereIn('tambol_id', $subDistrictCode)
			->get();
		return $subDistrict;
	}

	protected function disease() {
		$disease = DB::table('disease_ref')
			->orderBy('disease_id')
			->get()
			->toArray();
		return $disease;
	}

	protected function setDateFormat($jsDateRange='d/m/y - d/m/y') {
		$this->str = explode('-', $jsDateRange);
		$dReplace = str_replace('/', '-', $this->str);
		$result = array();
		foreach ($dReplace as $val) {
			$tmp = explode('-', $val);
			$tmp_rs = trim($tmp[2]).'-'.trim($tmp[1]).'-'.trim($tmp[0]);
			array_push($result, $tmp_rs);
		}
		return $result;
	}

}
