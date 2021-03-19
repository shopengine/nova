<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use App\Models\CodepoolGroup;
use App\Models\StatsCodepool;
use Illuminate\Support\Facades\DB;

class StatisticController extends ShopEngineNovaController
{
    public function stats(string $resource, string $resourceId)
    {
        $shop_setting_slug = $this->getShopSettings()->getSlug();

        switch ($resource) {
            case 'codepools': return StatsCodepool::stats($resourceId, $shop_setting_slug);
            case 'codepool-groups': return $this->codepoolGroup($resourceId, $shop_setting_slug);
        }

        return [];
    }

    public function statsByTime(string $resource, string $resourceId)
    {
        $shop_setting_slug = $this->getShopSettings()->getSlug();

        switch ($resource) {
            case 'codepools': return $this->codepoolByTime($resourceId, $shop_setting_slug);
            case 'codes': return $this->codeByTime($resourceId, $shop_setting_slug);
            case 'codepool-groups': return $this->codepoolGroupByTime($resourceId, $shop_setting_slug);
        }

        return [];
    }

    private function codepoolGroup($cId, string $shop_setting_slug)
    {
        $codepoolGroup = CodepoolGroup::find($cId);

        $stats = [
            'usageCount' => 0,

            'overall' => [
                'count' => 0,
                'grandTotal' => 0,
                'grandTotalAverage' => 0,
                'subTotal' => 0,
                'subTotalAverage' => 0,
                'discountTotal' => 0,
                'discountTotalAverage' => 0,
            ],
            'newCustomer' => [
                'count' => 0,
                'grandTotal' => 0,
                'grandTotalAverage' => 0,
            ],
            'secondCustomer' => [
                'count' => 0,
                'grandTotal' => 0,
                'grandTotalAverage' => 0,
            ],
        ];

        $overallIds = [];
        $newIds = [];
        $secondIds = [];

        foreach ($codepoolGroup->getCodepoolIds() as $id) {
            $s = StatsCodepool::stats($id, $shop_setting_slug, $overallIds, $newIds, $secondIds);

            foreach ($s['overallIds'] as $i) {
                $overallIds[] = $i->id;
            }

            foreach ($s['newIds'] as $i) {
                $newIds[] = $i->id;
            }

            foreach ($s['secondIds'] as $i) {
                $secondIds[] = $i->id;
            }

            foreach ($s as $groupKey => $value) {
                if ($groupKey === 'usageCount') {
                    $stats['usageCount'] += $value;
                    continue;
                }

                foreach ($s[$groupKey] as $k => $v) {
                    if (isset($stats[$groupKey][$k])) {
                        $stats[$groupKey][$k] += $v;
                    }
                }
            }
        }

        if ($stats['overall']['count'] == 0) {
            return $stats;
        }

        $overallCount = max($stats['overall']['count'], 1);
        $newCount = max($stats['newCustomer']['count'], 1);
        $secondCount = max($stats['secondCustomer']['count'], 1);

        $stats['overall']['grandTotalAverage'] = (int)round($stats['overall']['grandTotal'] / $overallCount);
        $stats['overall']['subTotalAverage'] = (int)round($stats['overall']['subTotal'] / $overallCount);
        $stats['overall']['discountTotalAverage'] = (int)round($stats['overall']['discountTotal'] / $overallCount);
        $stats['newCustomer']['discountTotalAverage'] = (int)round($stats['newCustomer']['grandTotal'] / $newCount);
        $stats['secondCustomer']['discountTotalAverage'] = (int)round($stats['secondCustomer']['grandTotal'] / $secondCount);

        return $stats;
    }

    private function codepoolByTime($cId, string $slug)
    {
        $last30Days = date('Y-m-d', strtotime('-30 days'));
        $last30Weeks = date('Y-m-d', strtotime('-30 weeks'));
        $last30Months = date('Y-m-d', strtotime('-30 months'));

        $statsByDay = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.codepool_id = $cId AND p.order_date > '$last30Days' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
        ");

        $statsByDayNewCustomer = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS c
            JOIN stats_purchases AS p ON c.purchase_id = p.id
            WHERE
                c.codepool_id = $cId AND p.order_date > '$last30Days' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
        ");

        $statsByWeek = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.codepool_id = $cId AND p.order_date > '$last30Weeks' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByWeekNewCustomer = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS c
            JOIN stats_purchases AS p ON c.purchase_id = p.id
            WHERE
                c.codepool_id = $cId AND p.order_date > '$last30Weeks' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonth = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.codepool_id = $cId AND p.order_date > '$last30Months' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonthNewCustomer = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS c
            JOIN stats_purchases AS p ON c.purchase_id = p.id
            WHERE
                c.codepool_id = $cId AND p.order_date > '$last30Months' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByDay = self::combine($statsByDay, $statsByDayNewCustomer);
        $statsByWeek = self::combine($statsByWeek, $statsByWeekNewCustomer);
        $statsByMonth = self::combine($statsByMonth, $statsByMonthNewCustomer);

        return [
            'statsByDay' => $statsByDay,
            'statsByWeek' => $statsByWeek,
            'statsByMonth' => $statsByMonth
        ];
    }

    private function codeByTime($codeId, string $slug)
    {
        $code = $this->getClient()->get("code/$codeId");
        $codeString = $code->getCode();

        $last30Days = date('Y-m-d', strtotime('-30 days'));
        $last30Weeks = date('Y-m-d', strtotime('-30 weeks'));
        $last30Months = date('Y-m-d', strtotime('-30 months'));

        $statsByDay = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.code = '$codeString' AND p.order_date > '$last30Days' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
        ");

        $statsByDayNewCustomer = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                pc.code = '$codeString' AND p.order_date > '$last30Days' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
        ");

        $statsByWeek = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.code = '$codeString' AND p.order_date > '$last30Weeks' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByWeekNewCustomer = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                pc.code = '$codeString' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonth = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE pc.code = '$codeString' AND p.order_date > '$last30Months' AND p.shop_setting_slug = '$slug'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonthNewCustomer = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM stats_purchase_codes AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                pc.code = '$codeString' AND p.shop_setting_slug = '$slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date AND p.shop_setting_slug = '$slug'
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByDay = self::combine($statsByDay, $statsByDayNewCustomer);
        $statsByWeek = self::combine($statsByWeek, $statsByWeekNewCustomer);
        $statsByMonth = self::combine($statsByMonth, $statsByMonthNewCustomer);

        return [
            'statsByDay' => $statsByDay,
            'statsByWeek' => $statsByWeek,
            'statsByMonth' => $statsByMonth
        ];
    }

    private static function combine($statsByDay, $statsNewCustomer)
    {
        $keyedStatsByDay = [];
        foreach ($statsByDay as $s) {
            $keyedStatsByDay[$s->DATE] = $s;
        }

        $keyedStatsNewCustomer = [];
        foreach ($statsNewCustomer as $s) {
            $keyedStatsNewCustomer[$s->DATE] = $s;
        }

        $orderCountByDate = [];

        foreach ($statsByDay as $key => $stat) {
            $orderCountByDate[$key] = (object)[
                'DATE' => $stat->DATE,
                'total' => $stat->total,
                'count' => $stat->count,
                'newCustomer' => 0
            ];

            if (isset($keyedStatsNewCustomer[$stat->DATE])) {
                $orderCountByDate[$key]->newCustomer = $keyedStatsNewCustomer[$stat->DATE]->count;
            }
        }

        return $orderCountByDate;
    }

    private function codepoolGroupByTime(string $resourceId, string $shop_setting_slug)
    {
        $codepoolGroup = CodepoolGroup::find($resourceId);
        $codepoolIds = implode(',', $codepoolGroup->getCodepoolIds());

        $last30Days = date('Y-m-d', strtotime('-30 days'));
        $last30Weeks = date('Y-m-d', strtotime('-30 weeks'));
        $last30Months = date('Y-m-d', strtotime('-30 months'));

        $statsByDay = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE p.order_date > '$last30Days'
            GROUP BY DATE
            ORDER BY p.order_date DESC
        ");

        $statsByDayNewCustomer = DB::select("
            SELECT
                   concat(DAY(p.order_date), '.', MONTH(p.order_date)) AS DATE,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                p.order_date > '$last30Days'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByWeek = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE p.order_date > '$last30Weeks'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByWeekNewCustomer = DB::select("
            SELECT
                   concat(WEEKOFYEAR(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                p.order_date > '$last30Weeks'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonth = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   sum(p.grand_total) AS total,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE p.order_date > '$last30Months'
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByMonthNewCustomer = DB::select("
            SELECT
                   concat(MONTH(p.order_date), '.', YEAR(p.order_date)) AS DATE,
                   count(*) AS count
            FROM (
                SELECT distinct purchase_id
	              FROM stats_purchase_codes
	              WHERE codepool_id IN ($codepoolIds)
            ) AS pc
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE
                p.order_date > '$last30Months'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id AND t.order_date < p.order_date
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id AND t.order_date < p.order_date
                )
            GROUP BY DATE
            ORDER BY p.order_date DESC
            LIMIT 30
        ");

        $statsByDay = $this->combine($statsByDay, $statsByDayNewCustomer);
        $statsByWeek = $this->combine($statsByWeek, $statsByWeekNewCustomer);
        $statsByMonth = $this->combine($statsByMonth, $statsByMonthNewCustomer);

        return [
            'statsByDay' => $statsByDay,
            'statsByWeek' => $statsByWeek,
            'statsByMonth' => $statsByMonth
        ];
    }
}
