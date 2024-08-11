<?php
namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $filePath;

    public function __construct(Campaign $campaign, $filePath)
    {
        $this->campaign = $campaign;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $csv = Reader::createFromPath(storage_path('app/' . $this->filePath), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $total = iterator_count($records);

        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        $this->campaign->update(['total_recipients' => $total]);

        foreach ($records as $record) {
            // Check if the 'name' and 'email' keys exist in the record
            if (!isset($record['name']) || !isset($record['email'])) {
                Log::error('Missing name or email in record', $record);
                continue; // Skip this record and continue with the next one
            }

            $name = $record['name'];
            $email = $record['email'];

            Mail::raw("Hello {{username}}, this is a test email.", function ($message) use ($email, $name) {
                $message->to($email)
                        ->subject('Campaign Email')
                        ->setBody(str_replace('{{username}}', $name, 'Hello {{username}}, this is a test email.'));
            });

            $this->campaign->increment('processed_recipients');
        }

        $this->campaign->update(['status' => 'completed']);
    }
}
