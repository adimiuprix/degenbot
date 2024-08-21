<?php

namespace App\Controllers;
use Telegram\Bot\Keyboard\Keyboard;
use App\Controllers\Start;
use App\Controllers\Registration;
use App\Models\Members;

class Home extends BaseController
{
    protected $start;
    protected $member;
    protected $fetchObject;
    protected $regist;

    public function __construct()
    {
        $this->fetchObject = file_get_contents('php://input');
        $this->member = new Members();
        $this->start = new Start();
        $this->regist = new Registration();
    }

    public function index(){
        $data = $this->setting()['project_name'];
        dd($data);
    }

    public function telegram(){
        $decoded_data = json_decode($this->fetchObject, true);
        $text = $decoded_data['message']['text'];
        $username = $decoded_data['message']['from']['username'];
        $chatID = $decoded_data['message']['from']['id'];

        file_put_contents(WRITEPATH . 'data.json', json_encode(json_decode($this->fetchObject, true), JSON_PRETTY_PRINT));

        if($text == '/start'){
            $this->start->start($this->telegram, $chatID, $username);
        }

        if($text == 'Join Airdrop'){
            $this->start->join($this->telegram, $chatID);
        }

        if($text == 'Registration'){
            $this->regist->registration($this->telegram, $chatID, $username);
        }

        if($text == 'Set Email'){
            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Masukkan email anda",
            ]);
        }

        if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
            $this->is_valid_email($chatID, $text);
        }

        if($text == 'Next'){
            $this->task($this->telegram, $chatID);
        }

        if($text == 'Menu'){
            $this->menus($chatID);
        }

        if($text == 'Submit Answer'){
            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Belum di buat halamannya",
            ]);
        }

        if($text == 'My Balance'){
            $this->balance($chatID);
        }

        if($text == 'Main Menu')
        {
            $menu = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Join Airdrop'),
                Keyboard::button('My Balance'),
                Keyboard::button('Information'),
            ]);

            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "🖱 Click one of the buttons below",
                'reply_markup' => $menu
            ]);
        }

        if($text == 'Information')
        {
            $menu = Keyboard::make()
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->row([
                    Keyboard::button('Join Airdrop'),
                    Keyboard::button('My Balance'),
                    Keyboard::button('Information'),
                ]);

            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "♻️ You can change your registration details by typing /changeprofile\n"
                . "🔐 Subscribers who unfollow the mandatory social media tasks will not be eligible.\n"
                . "👨‍👩‍👧 $3000 worth of ACE for top 200 referrers.\n"
                . "🔄 Audit: Cyberscope\n"

                . "🗞 Notes: Airdrop will end on June 25, 2024. Total airdrop pool is $10,000 worth of ACE. 1000 participants will be selected randomly to be rewarded $6 worth of ACE each. Top 200 referrers will be rewarded as follows.\n"

                . "1st place:          $125 worth of ACE\n"
                . "2nd place:          $75 worth of ACE\n"
                . "3rd place:          $42 worth of ACE\n"
                . "4th to 200th place: $14 worth of ACE each.\n"

                . "⏳ Distribution date: June 30, 2024.\n",
                'reply_markup' => $menu,
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    function is_valid_email($chatID, $email)
    {
        // Using PHP's built-in function to check email validity
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $memberModel = $this->member->where('chat_id', $chatID)->first();

            $this->member->update($memberModel['id'], [
                'email' => $email,
            ]);

            $true_email = Keyboard::make()
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->row([
                    Keyboard::button('Next'),
                    Keyboard::button('Menu'),
                ]);

            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Email Benar",
                'reply_markup' => $true_email
            ]);
        } else {
            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Wrong email!...",
            ]);
        }
    }

    public function task($telegram, $chatID) :void
    {
        $submit = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Submit Answer'),
            ]);

        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "✏️ Mandatory Tasks:\n\n"
            . "🔹 Join our [channel](https://www.google.com)\n"
            . "🔹 Visit our website to mine\n"
            . "🔹 Join our Discord Server\n"
            . "🔹 Follow our Twitter page\n"
            . "🔹 Join our Airdrop Partner's Channel\n"
            . "🔹 Follow our Airdrop Partner's Twitter and Retweet this Airdrop Tweet",
            'parse_mode' => 'Markdown',
            'reply_markup' => $submit
        ]);
    }

    public function menus($chatID){
        $menu = Keyboard::make()
        ->setResizeKeyboard(true)
        ->setOneTimeKeyboard(true)
        ->row([
            Keyboard::button('Join Airdrop'),
            Keyboard::button('My Balance'),
            Keyboard::button('Information'),
        ]);

        $this->telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "🖱 Click one of the buttons below",
            'reply_markup' => $menu
        ]);
    }

    public function balance($chatID)
    {
        $data = $this->member->where('chat_id', $chatID)->first();
        $bal = $data['balance'];

        $this->telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "🏆 Reward: $bal worth of ACE\n"
            . "👨‍👩‍👧 Referral number: 0\n"

            . "referral link: [https://t.me/ApolloCapsAirdropBot?start=1795676886](https://t.me/ApolloCapsAirdropBot?start=1795676886)",
            'parse_mode' => 'Markdown',
        ]);
    }
}
