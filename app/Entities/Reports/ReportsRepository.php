<?php

namespace App\Entities\Reports;

use App\Entities\BaseRepository;
use App\Entities\Payments\Payment;
use App\Entities\Projects\Project;
use DB;

/**
 * Reports Repository Class
 *
 * @author Nafies Luthfi <nafiesL@gmail.com>
 */
class ReportsRepository extends BaseRepository
{
    protected $model;

    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function getDailyReports($date, $q)
    {
        return Payment::orderBy('date', 'desc')
            ->where('date', $date)
            ->with('partner', 'project')
            ->get();
    }

    public function getMonthlyReports($year, $month)
    {
        return Payment::select(DB::raw("date, count(`id`) as count, sum(if(in_out = 1, amount, 0)) AS cashin, sum(if(in_out = 0, amount, 0)) AS cashout, in_out"))
            ->where(DB::raw('YEAR(date)'), $year)
            ->where(DB::raw('MONTH(date)'), $month)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getYearlyReports($year)
    {
        return Payment::select(DB::raw("MONTH(date) as month, count(`id`) as count, sum(if(in_out = 1, amount, 0)) AS cashin, sum(if(in_out = 0, amount, 0)) AS cashout, in_out"))
            ->where(DB::raw('YEAR(date)'), $year)
            ->groupBy(DB::raw('YEAR(date)'))
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getCurrentCredits()
    {
        // On Progress, Done, On Hold
        $projects = Project::whereIn('status_id', [2, 3, 6])->with('payments', 'customer')->get();
        return $projects->filter(function ($project) {
            return $project->cashInTotal() < $project->project_value;
        })->values();
    }

}
