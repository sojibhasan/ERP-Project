<?php

namespace Modules\Superadmin\Http\Controllers;

use \Notification;
use App\System;
use App\User;
use App\Utils\Util;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Modules\Superadmin\Notifications\NewBusinessNotification;
use Modules\Superadmin\Notifications\NewBusinessWelcomNotification;
use Modules\Superadmin\Notifications\NewVisitorWelcomNotification;

class DataController extends Controller
{
    /**
     * Parses notification message from database.
     * @return array
     */
    public function parse_notification($notification)
    {
        $notification_data = [];
        if (
            $notification->type ==
            'Modules\Superadmin\Notifications\SendSubscriptionExpiryAlert'
        ) {
            $data = $notification->data;
            $msg = __('superadmin::lang.subscription_expiry_alert', ['days_left' => $data['days_left'], 'app_name' => config('app.name')]);

            $notification_data = [
                'msg' => $msg,
                'icon_class' => "fa fa-exclamation-triangle text-yellow",
                'link' =>  action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index'),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        }

        return $notification_data;
    }

    /**
     * Function to be called after a new business is created.
     * @return null
     */
    public function after_business_created($data)
    {
        try {
            //Send new registration notification to superadmin
            $is_notif_enabled =
                System::getProperty('enable_new_business_registration_notification');

            $common_util = new Util();

            if (!$common_util->IsMailConfigured()) {
                return null;
            }

            $email = System::getProperty('email');
            $business = $data['business'];

            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                    ->notify(new NewBusinessNotification($business));
            }

            //Send welcome email to business owner
            $welcome_email_settings = System::getProperties(['enable_welcome_email', 'welcome_email_subject', 'welcome_email_body'], true);

            if (isset($welcome_email_settings['enable_welcome_email']) && $welcome_email_settings['enable_welcome_email'] == 1 && !empty($welcome_email_settings['welcome_email_subject']) && !empty($welcome_email_settings['welcome_email_body'])) {
                $subject = $this->removeTags($welcome_email_settings['welcome_email_subject'], $business);
                $body = $this->removeTags($welcome_email_settings['welcome_email_body'], $business);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body
                ];

                Notification::route('mail', $business->owner->email)
                    ->notify(new NewBusinessWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        return null;
    }
    /**
     * Function to be called after a new customer is created.
     * @return null
     */
    public function after_customer_created($data)
    {
        try {
            $customer = $data['customer'];
            //Send welcome email to customer
            $welcome_email_settings = System::getProperties(['enable_customer_welcome_email', 'customer_welcome_email_subject', 'customer_welcome_email_body'], true);

            if (isset($welcome_email_settings['enable_customer_welcome_email']) && $welcome_email_settings['enable_customer_welcome_email'] == 1 && !empty($welcome_email_settings['customer_welcome_email_subject']) && !empty($welcome_email_settings['customer_welcome_email_body'])) {
                $subject = $this->removeCustomerTags($welcome_email_settings['customer_welcome_email_subject'], $customer);
                $body = $this->removeCustomerTags($welcome_email_settings['customer_welcome_email_body'], $customer);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body
                ];

                Notification::route('mail', $customer->email)
                    ->notify(new NewBusinessWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        return null;
    }

    /**
     * Function to be called after a new visitor is created.
     * @return null
     */
    public function after_visitor_created($data)
    {
        try {
            $visitor = $data['visitor'];
            //Send welcome email to visitor
            $welcome_email_settings = System::getProperties(['enable_visitor_welcome_email', 'visitor_welcome_email_subject', 'visitor_welcome_email_body'], true);

            if (isset($welcome_email_settings['enable_visitor_welcome_email']) && $welcome_email_settings['enable_visitor_welcome_email'] == 1 && !empty($welcome_email_settings['visitor_welcome_email_subject']) && !empty($welcome_email_settings['visitor_welcome_email_body'])) {
                $subject = $this->removeVisitorTags($welcome_email_settings['visitor_welcome_email_subject'], $visitor);
                $body = $this->removeVisitorTags($welcome_email_settings['visitor_welcome_email_body'], $visitor);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body
                ];

                Notification::route('mail', $visitor->email)
                    ->notify(new NewVisitorWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        return null;
    }

    private function removeVisitorTags($string, $visitor)
    {
        $url = URL::to('/');

        $content = 'Please login to the system in <a href="' . $url . '">' . $url . '</a>';

        $string = str_replace('{visitor_name}', $visitor->name, $string);
        $string = str_replace('{username}', $visitor->username, $string);


        return $string;
    }

    private function removeCustomerTags($string, $customer)
    {
        $url = URL::to('/');

        $content = 'Please login to the system in <a href="' . $url . '">' . $url . '</a>';

        $string = str_replace('{customer_name}', $customer->first_name . ' ' . $customer->last_name, $string);
        $string = str_replace('{username}', $customer->username, $string);


        return $string;
    }

    private function removeTags($string, $business)
    {
        $url = URL::to('/');
        if ($business->is_patient) {
            $username = User::where('business_id', $business->id)->first()->username;
            $content = 'Patient Code: ' . $username . '</br> Please login to the System in <a href="' . $url . '">' . $url . '</a>';
        } else {
            $content = 'Please login to the system in <a href="' . $url . '">' . $url . '</a>';
        }
        $string = str_replace('{business_name}', $business->name, $string);
        $string = str_replace('{owner_name}', $business->owner->user_full_name, $string);
        $string = str_replace('{username}', $content, $string);
        $string = str_replace('{company_number}', $business->company_number, $string);


        return $string;
    }

    /**
     * Function to be called after a new agent is created.
     * @return null
     */
    public function after_agent_created($data)
    {
        try {
            $agent = $data['agent'];
            //Send welcome email to business owner
            $welcome_email_settings = System::getProperties(['enable_welcome_email', 'agent_welcome_email_subject', 'agent_welcome_email_body'], true);

            if (isset($welcome_email_settings['enable_welcome_email']) && $welcome_email_settings['enable_welcome_email'] == 1 && !empty($welcome_email_settings['agent_welcome_email_subject']) && !empty($welcome_email_settings['agent_welcome_email_body'])) {
                $subject = $this->removeAgentTags($welcome_email_settings['agent_welcome_email_subject'], $agent);
                $body = $this->removeAgentTags($welcome_email_settings['agent_welcome_email_body'], $agent);

                $welcome_email_data = [
                    'subject' => $subject,
                    'body' => $body
                ];

                Notification::route('mail', $agent->email)
                    ->notify(new NewBusinessWelcomNotification($welcome_email_data));
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        return null;
    }

    private function removeAgentTags($string, $agent)
    {

        $string = str_replace('{agent_name}', $agent->name, $string);
        $string = str_replace('{username}', $agent->username, $string);
        $string = str_replace('{referral_code}', $agent->referral_code, $string);


        return $string;
    }
}
