<?php


namespace App\Utilities;


class CSTransactionalEmail
{
    protected $provider;

    public function __construct($smartEmailId)
    {
        $this->provider = new \CS_REST_Transactional_SmartEmail($smartEmailId, env('CAMPAIGN_MONITOR_KEY'));
    }

    public static function send($smartEmailId, $recipientEmail, $recipientName = null, $data = null)
    {
        $email = new static($smartEmailId);
        $content = [];
        $content["To"] = "{$recipientName} <{$recipientEmail}>";
        $content["Data"] = [];
        foreach ($data as $key => $value) {
            $content["Data"][$key] = $value;
        }
        return $email->provider->send($content);
    }
}