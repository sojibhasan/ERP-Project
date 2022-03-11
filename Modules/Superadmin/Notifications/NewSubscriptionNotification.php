<?php

namespace Modules\Superadmin\Notifications;

use App\System;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class NewSubscriptionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subscription = $this->subscription;
        if($subscription->paid_via == 'offline'){
            $mail_subject = System::getProperty('new_subscription_email_subject_offline');
            $mail_body = System::getProperty('new_subscription_email_subject_offline');
        }else{
            $mail_subject = System::getProperty('new_subscription_email_subject');
            $mail_body = System::getProperty('new_subscription_email_body');
        }
        $details = $this->removeTags($mail_body,  $this->subscription);

        $settings = DB::table('site_settings')->where('id', 1)->select('uploadFileLLogo')->first();
        $img_url = $settings->uploadFileLLogo;
        return (new MailMessage)
            ->subject($mail_subject)
            ->view('notification_template.partials.new_subscription', [
                'details' => $details,
                'url_img' => $img_url,

            ]);
    }

    private function removeTags($string, $subscription)
    {
        $paid_via = !empty($subscription->paid_via) ? $subscription->paid_via : 'Free';

        $string = str_replace('{business_name}', $subscription->business->name, $string);
        $string = str_replace('{company_code}', $subscription->business->company_number, $string);
        $string = str_replace('{package_name}', $subscription->package->name, $string);
        $string = str_replace('{transaction_id}', $subscription->payment_transaction_id, $string);
        $string = str_replace('{status}', ucfirst($subscription->status), $string);
        $string = str_replace('{paid_via}', $paid_via, $string);
       

        return $string;
    }


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
