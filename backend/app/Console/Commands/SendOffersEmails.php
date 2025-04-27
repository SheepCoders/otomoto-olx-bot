<?php

namespace App\Console\Commands;

use App\Models\Filter;
use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOffersEmails extends Command
{
    protected $signature = 'offers:send-emails';
    protected $description = 'Send new offers to users based on their filters';

    public function handle()
    {
        $this->info('Starting crawler...');

        $pythonPath = base_path('crawler-venv/bin/python');
        $crawlerScript = base_path('crawler/crawler.py');

        exec("$pythonPath $crawlerScript", $output, $result);

        if ($result !== 0) {
            $this->error('Crawler failed to run.');
            return 1;
        }

        $this->info('Crawler finished, proceeding to send emails.');

        $users = Filter::select('user_email')->distinct()->get();

        foreach ($users as $user) {
            $filters = Filter::where('user_email', $user->user_email)->get();
            $filterIds = $filters->pluck('id')->toArray();

            $offers = Offer::whereIn('filter_id', $filterIds)
                ->where('created_at', '>=', now()->subDays(1))
                ->get();

            if ($offers->count() > 0) {
                $this->sendOffersEmail($user->user_email, $offers);

                foreach ($filters as $filter) {
                    $filter->last_sent_at = now();
                    $filter->save();
                }

                $this->info("Sent offers to {$user->user_email}");
            } else {
                $this->info("No new offers for {$user->user_email}");
            }
        }
    }

    protected function sendOffersEmail($email, $offers)
    {
        $html = '<h1>Nowe oferty</h1>';
        $html .= '<table style="width:100%; border-collapse:collapse;">';
        $html .= '<thead><tr><th style="text-align:left; padding:8px; border-bottom:2px solid #ddd;">Zdjęcie</th><th style="text-align:left; padding:8px; border-bottom:2px solid #ddd;">Tytuł</th><th style="text-align:left; padding:8px; border-bottom:2px solid #ddd;">Cena</th><th style="text-align:left; padding:8px; border-bottom:2px solid #ddd;">Link</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($offers as $offer) {
            $image = $offer->image_url ?? 'https://via.placeholder.com/120x90?text=Brak+zdjecia';

            $html .= "
                <tr>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'><img src='{$image}' alt='Zdjęcie' style='width:120px; height:auto; border-radius:4px;'></td>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'>{$offer->title}</td>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'>{$offer->price}</td>
                    <td style='padding:8px; border-bottom:1px solid #ddd;'><a href='{$offer->offer_url}' style='color:#1E88E5;'>Zobacz ofertę</a></td>
                </tr>";
        }

        $html .= '</tbody></table>';

        Mail::send([], [], function ($message) use ($email, $html) {
            $message->to($email)
                ->subject('Nowe oferty z OLX i Otomoto')
                ->html($html);
        });
    }
}
