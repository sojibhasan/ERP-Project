<?php

namespace Modules\Superadmin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class SubscriptionOfflinePaymentActivationConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($business, $package)
    {
        $this->business = $business;
        $this->package = $package;
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
        $details = 'Business: ' . $this->business->name . ', CompanyCode: ' . $this->business->company_number . ', Package: ' . $this->package->name . ', Price: ' . $this->package->price;
        $settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
        $img_url = $settings->uploadFileLLogo;
        return (new MailMessage)
            ->view('notification_template.partials.offline_payment', [
                'url_img' => $img_url,
                'details' => $details,

            ]);
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
