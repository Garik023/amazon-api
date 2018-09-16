<?php

namespace App\Http\Controllers;

use App\Reports;
use App\Unzipper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const API_ENDPOINT = 'https://api.amazon.com/';
    const AD_API_ENDPOINT = 'https://advertising-api.amazon.com/v1/';
    const CLIENT_ID = 'amzn1.application-oa2-client.ed236f4ff11f4616a4f483dbd5256c50';
    const CLIENT_SECRET = '9aae8c8f4cf77994c84b6697d32d183fe5def95dcbb8b46001b0f0f3bdbabcb2';
    const REFRESH_TOKEN = 'Atzr|IwEBINV5AnCbchaTz8vJCI934Raip1YDZbk18VgHA5Sb8K4ivoA3Rf8hl_Ju5ks4_sBDdA7NW6FM0xGyosxpmj2v8vJQI1U3WQKz9swXvmUukn_AAOBO7jqmq6WV-UMc2DgNb1o6TOOVA3PZIlg_ZwzHjPkyHB-v7diAs9Y2ANoqm4Ro0YZ69ZpU4tMB8kZlspCKJib3k6jSyySMnqIMLT9osH_Z-BGF2SUJVlHTyXdDNym6XCxfEeouJtEHjwayoiRGJ8qyKEnBd6JoLFWhW4kauW9dFVFpd7YXOhh0n0kDUKYsxj8afxQ6Bv9VPIJYPTwfvut9iyW46BFCbgUT8ZM358TsQ-JquzY-xG_GSFIJsqPWIdgHI58A6CxJrpXs-3gPmlv_6dww_YWsOlpaZ58m5KlRVpNkirV8fFPacw6dujrGJYtX3HTxnJgMnzT7hjRc1ODof-gFiYbcWzKtbJcVfd7ZkyT7kiVDpnFpHuhVv0kzuLY4N4T8jK14Ry6y-C4At__IkgoemkVvy7Q_VMDmae-PSWwvrZsMhGq7ccHuzAZPa8lsJQ0ymj4apDHQtN7DtynDlX6BZpQTacdOK8YYOX_M';

    public function index()
    {
        $data['reports'] = Reports::getAllReportsData();
        return view('welcome', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function reportDetails(Request $request)
    {
        $reportId = $request->input('id');
        $report = Reports::where('id' , $reportId)->first()->toArray();
        if(empty($report)){
            return redirect('/');
        }
        $reportData = self::getFileContentAsArray($report['json_file_path']);
        return view('singleReport', ['reportData' => $reportData]);

    }

    /**
     * Get json data and return as array
     * @param $fileName
     * @return mixed
     */
    public static function getFileContentAsArray($fileName)
    {
        $fileData = file_get_contents('./extractedFiles/'.$fileName);
        return json_decode($fileData, true);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function generateReport(Request $request)
    {
        $accessToken = self::getAccessToken();
        $reportDate = $request->input('reportDate');

        $data = array(
            'campaignType' => "sponsoredProducts",
            'metrics' => 'campaignName,campaignId,campaignType,campaignStatus,campaignBudget,campaignBudgetType,attributedSales14d,attributedSales14dSameSKU,attributedConversions14d,impressions,clicks,cost',
            'reportDate' => $reportDate,
            'segment' => 'placement'
        );
        $reportId = self::getCampaignReport($accessToken, $data);
        sleep(5);// amazon didn't get ready the report right after request, so we need to sleep a few seconds
        $location = self::getGeneratedStatusInfo($accessToken, $reportId);

        if (!$location) {
            return response()->json([
                'success' => 'false',
                'message' => 'Something went wrong.'
            ]);
        }

        $reportArchiveName = self::downloadToLocalArchive($accessToken, $location);

        $jsonFileName = self::unzipArchive($reportArchiveName);

        $reportName = self::generateRandomString(5);
        $report = new Reports(
            [
                'name' => $reportName,
                'zip_file_location' => $reportArchiveName,
                'date_period' => $reportDate,
                'json_file_path' => $jsonFileName
            ]
        );
        $report->save();
        return response()->json([
            'success' => 'true',
            'data' => $report
        ]);

    }

    /***
     * Check report readiness
     * @param $accessToken
     * @param $reportId
     * @return bool
     */
    public static function getGeneratedStatusInfo($accessToken, $reportId)
    {
        $crl = curl_init(self::AD_API_ENDPOINT . 'reports/' . $reportId);

        $header[] = 'Content-type: application/json';
        $header[] = 'charset:UTF-8';
        $header[] = 'Authorization:Bearer ' . $accessToken;
        $header[] = 'Amazon-Advertising-API-Scope:3737894909237421';
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($crl);
        $result = json_decode($output, true);

        if ($result['status'] == "SUCCESS") {
            return $result['location'];
        }
        return false;
    }

    /**
     * Donwnload file from Amazon to local Archive
     * @param $accessToken
     * @param $reportId
     * @return string
     */
    public static function downloadToLocalArchive($accessToken, $reportId)
    {
        $crl = curl_init($reportId);
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'charset:UTF-8';
        $header[] = 'Authorization:Bearer ' . $accessToken;
        $header[] = 'Amazon-Advertising-API-Scope:3737894909237421';
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_ENCODING, 'gzip');
        $output = curl_exec($crl);
        $rest = curl_getinfo($crl);
        curl_close($crl);

        $archiveFileName = self::generateRandomString(12) . '.json.gz';
        file_put_contents('./archives/' . $archiveFileName, fopen($rest['redirect_url'], 'r'));
        return $archiveFileName;
    }

    /**
     * Unzip Archive to json file
     * @param $archiveName
     * @return string
     */

    public static function unzipArchive($archiveName)
    {
        $GLOBALS['status'] = array();
        $unzipper = new Unzipper;
        $jsonFileName = self::generateRandomString(13) . '.json';
        $unzipper::extractGzipFile('./archives/' . $archiveName, './extractedFiles/' . $jsonFileName);
        return $jsonFileName;
    }

    /**
     * Get Access token to make requests
     * @return mixed
     */
    public static function getAccessToken()
    {
        $crl = curl_init(self::API_ENDPOINT . '/auth/o2/token');
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'charset:UTF-8';
        $data = array(
            'grant_type' => 'refresh_token',
            'client_id' => self::CLIENT_ID,
            'refresh_token' => self::REFRESH_TOKEN,
            'client_secret' => self::CLIENT_SECRET
        );

        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($data));

        $rest = curl_exec($crl);
        curl_close($crl);

        $result = json_decode($rest, true);
        return $result['access_token'];
    }

    /**
     * Create report
     * @param $accessToken
     * @param $data
     * @return mixed
     */
    public static function getCampaignReport($accessToken, $data)
    {
        ini_set("zlib.output_compression", "Off");

        $crl = curl_init(self::AD_API_ENDPOINT . 'campaigns/report');
        $header = array();
        $header[] = 'Content-type: application/json';
        $header[] = 'charset:UTF-8';
        $header[] = 'Authorization:Bearer ' . $accessToken;
        $header[] = 'Amazon-Advertising-API-Scope:3737894909237421';
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($data));
        $rest = curl_exec($crl);
        curl_close($crl);
        $result = json_decode($rest, true);

        return $result['reportId'];

    }


    /**
     * Function to generate random string
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
