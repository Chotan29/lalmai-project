<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Seeds the 3 new billing alert events into alert_settings.
 * Run: php artisan db:seed --class=BillingAlertSettingSeeder
 *
 * Uses INSERT IGNORE pattern (updateOrInsert) so it's safe to re-run.
 */
class BillingAlertSettingSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $events = [
            [
                'event'          => 'BillingGenerated',
                'subject'        => 'New Bill Generated',
                'template'       => 'Dear {{name}} ({{reg_no}}), your {{period}} fees of BDT {{amount}} have been added. Due date: {{due_date}}. Please pay on time to avoid fines. — {{app_name}}',
                'email_template' => 'Dear {{name}},<br><br>Your <strong>{{period}}</strong> fees of <strong>BDT {{amount}}</strong> have been generated.<br>Due Date: <strong>{{due_date}}</strong><br><br>Fee Details: {{fee_heads}}<br><br>Please pay on time to avoid late fines.<br><br>— {{app_name}}',
            ],
            [
                'event'          => 'BillingDueReminder',
                'subject'        => 'Fee Payment Reminder',
                'template'       => 'Dear {{name}} ({{reg_no}}), your fee of BDT {{amount}} is due on {{due_date}} ({{days_left}} days left). Please pay promptly to avoid fines. — {{app_name}}',
                'email_template' => 'Dear {{name}},<br><br>This is a reminder that your fee of <strong>BDT {{amount}}</strong> is due on <strong>{{due_date}}</strong> ({{days_left}} days remaining).<br><br>Please contact the Accounts Department for payment.<br><br>— {{app_name}}',
            ],
            [
                'event'          => 'BillingOverdue',
                'subject'        => 'Fee Payment Overdue',
                'template'       => 'Dear {{name}} ({{reg_no}}), your fee of BDT {{amount}} is OVERDUE. Fine applied: BDT {{fine_amount}}. Please pay immediately to avoid further penalties. — {{app_name}}',
                'email_template' => 'Dear {{name}},<br><br>Your fee of <strong>BDT {{amount}}</strong> is <strong>OVERDUE</strong>.<br>Fine Applied: <strong>BDT {{fine_amount}}</strong><br><br>Please contact the Accounts Department immediately.<br><br>— {{app_name}}',
            ],
        ];

        foreach ($events as $event) {
            DB::table('alert_settings')->updateOrInsert(
                ['event' => $event['event']],
                [
                    'created_by'     => 1,
                    'event'          => $event['event'],
                    'sms'            => 1,
                    'email'          => 0,
                    'subject'        => $event['subject'],
                    'template'       => $event['template'],
                    'email_template' => $event['email_template'],
                    'status'         => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]
            );
        }

        $this->command->info('✅ 3 billing alert events seeded: BillingGenerated, BillingDueReminder, BillingOverdue');
    }
}
