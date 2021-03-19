<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use App\Models\CodepoolGroup;
use Illuminate\Support\Facades\DB;

class ExportController extends ShopEngineNovaController
{
    public function orders(string $resource, string $id)
    {
        switch ($resource) {
            case 'codepools': return $this->codepoolOrders($id);
            case 'codes': return $this->codeOrders($id);
            case 'codepool-groups': return $this->codepoolGroupsOrders($id);
        }

        return '';
    }

    public function newCustomer(string $resource, string $id)
    {
        switch ($resource) {
            case 'codepools': return $this->codepoolNewCustomer($id);
            case 'codes': return $this->codeNewCustomer($id);
            case 'codepool-groups': return $this->codepoolGroupsNewCustomer($id);
        }

        return '';
    }

    private function codepoolOrders(string $cId = '')
    {
        $title = 'Email Liste';
        $shop_setting_slug = $this->getShopSettings()->getSlug();

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchases AS p
            JOIN stats_purchase_codes AS pc ON pc.purchase_id = p.id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE pc.codepool_id = $cId AND p.shop_setting_slug = '$shop_setting_slug'
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - Orders', $emails);
    }

    private function codeOrders(string $code)
    {
        $title = 'Email Liste';
        $shop_setting_slug = $this->getShopSettings()->getSlug();

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchases AS p
            JOIN stats_purchase_codes AS pc ON pc.purchase_id = p.id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE pc.code = '$code' AND p.shop_setting_slug = '$shop_setting_slug'
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - Orders', $emails);
    }

    private function codepoolNewCustomer(string $cId = '')
    {
        $title = 'Email Liste';
        $shop_setting_slug = $this->getShopSettings()->getSlug();

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchase_codes pc
            JOIN stats_purchases p ON p.id = pc.purchase_id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE
                pc.codepool_id = $cId AND p.shop_setting_slug = '$shop_setting_slug'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id
                )
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - NewCustomer', $emails);
    }

    private function codeNewCustomer(string $code)
    {
        $title = 'Email Liste';

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchase_codes pc
            JOIN stats_purchases p ON p.id = pc.purchase_id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE
                pc.code = '$code'
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id
                )
                AND NOT EXISTS (
                    SELECT * FROM stats_purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id
                )
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - Orders', $emails);
    }

    public function codepoolActiveCodes(string $cId = '')
    {
        $codes = $this->getClient()->get('code', [
            'codepoolId-eq' => $cId,
            'status-eq' => 'enabled',
            'properties' => 'code'
        ]);

        $output = [];

        foreach ($codes as $code) {
            $output[] = $code->getCode();
        }

        header('Content-Type: text/csv');
        header('Content-disposition: attachment; filename="codes.csv"');
        print(implode("\n", $output));
        die();
    }


    private function echoEmailCSV($fileName, $emails)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $out = fopen('php://output', 'w');
        foreach ($emails as $row) {
            fputcsv($out, [current($row)]);
        }
        fclose($out);
        exit;
    }

    private function codepoolGroupsOrders(string $id)
    {
        $title = 'Email Liste';

        $codepoolGroup = CodepoolGroup::find($id);
        $codepoolIds = implode(',', $codepoolGroup->getCodepoolIds());

        $shop_setting_slug = \Shop::settings()->getSlug();

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchases AS p
            JOIN stats_purchase_codes AS pc ON pc.purchase_id = p.id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE pc.codepool_id IN ($codepoolIds) AND p.shop_setting_slug = '$shop_setting_slug'
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - Orders', $emails);
    }

    private function codepoolGroupsNewCustomer(string $id)
    {
        $title = 'Email Liste';

        $codepoolGroup = CodepoolGroup::find($id);
        $codepoolIds = implode(',', $codepoolGroup->getCodepoolIds());
        $shop_setting_slug = \Shop::settings()->getSlug();

        $emails = DB::select("
            SELECT DISTINCT p.customer_email, pcp.name
            FROM stats_purchase_codes pc
            JOIN stats_purchases p ON p.id = pc.purchase_id
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            WHERE
                pc.codepool IN ($codepoolIds) AND p.shop_setting_slug = '$shop_setting_slug'
                AND NOT EXISTS (
                    SELECT * FROM purchases t
                    WHERE t.customer_email = p.customer_email AND t.id != p.id
                )
                AND NOT EXISTS (
                    SELECT * FROM purchases t
                    WHERE t.customer_name = p.customer_name AND t.customer_postcode = p.customer_postcode AND t.id != p.id
                )
        ");

        if (isset($emails[0]->name)) {
            $title = $emails[0]->name;
        }

        return $this->echoEmailCSV($title . ' - Orders', $emails);
    }
}
