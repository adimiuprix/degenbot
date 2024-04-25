<?php

namespace App\Controllers;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;

use App\Models\Members;
use App\Models\Email;

class Home extends BaseController
{
    public function index(){
        $telegram = new Api('7154738294:AAEHz94hn2LLbic7FhJBhD0WghdBWHUPNBs');
        $response = $telegram->addCommand(\Telegram\Bot\Commands\HelpCommand::class);
        $update = $telegram->commandsHandler(true);
        dd($update);
    }

    public function telegram(){
        $telegram = new Api('7154738294:AAEHz94hn2LLbic7FhJBhD0WghdBWHUPNBs');
        // Ambil data dari input Telegram
        $data = file_get_contents('php://input');

        // Decode data JSON ke dalam bentuk array
        $decoded_data = json_decode($data, true);
        $file = WRITEPATH . 'data.json';

        // Encode array ke dalam format JSON
        $json_encoded = json_encode($decoded_data, JSON_PRETTY_PRINT);
        file_put_contents($file, $json_encoded);

        if(isset($decoded_data)){
            $username = $decoded_data['message']['from']['username'];
            $chatID = $decoded_data['message']['from']['id'];
            $message = $decoded_data['message']['text'];

            switch ($message) {
                case '/start':
                    $this->start($telegram, $chatID, $username);
                    break;

                case 'Join Airdrop':
                    $this->join($telegram, $chatID);
                    break;

                case 'Registration':
                    $this->registration($telegram, $chatID, $username);
                    break;

                case 'Set Email':
                    $telegram->sendMessage([
                        'chat_id' => $chatID,
                        'text' => "Masukkan email anda",
                    ]);
                    break;

                case $this->is_valid_email($message):
                    if ($this->is_valid_email($message)){
                        $this->registResult($telegram, $chatID, $username, $message);
                    }else{
                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => "Bukan emmail",
                        ]);
                    }
                    break;

                case 'Next':
                    $this->task($telegram, $chatID, $username);
                    break;

                // case 'Menu':
                //     $this->start($telegram, $chatID, $username);
                //     break;
            }
        }
    }

    public function start($telegram, $chatID, $username)
    {
        $key1 = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Join Airdrop'),
                Keyboard::button('My Balance'),
                Keyboard::button('Information'),
            ]);

        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "🤝 Welcome $username! I will guide you away from our airdrop.

            1️⃣ First, click the Join Airdrop & Register button to perform the airdrop tasks and submit your information from the Register button.

            2️⃣ You can check your balance and get your referral link by using the My Balance button. 

            3️⃣ Please make sure that you have read the Information section.

            🗞 Note: You can change your profile details by typing /changeprofile",
            'reply_markup' => $key1
        ]);

        return true;
    }

    public function join($telegram, $chatID)
    {
        $key2 = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Registration'),
            ]);

        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "💻 Please perform the @airdrop tasks to earn up to 300 Yoda.

                💠 Solve the portal captcha to join BabyYoda Telegram group.

                💠 Follow BabyYoda on Twitter (https://twitter.com/BabyYodaonton) and retweet the pinned post by tagging 3 of your friends.

                💠 Join our promoter channel. (https://t.me/Airdrop) (Optional » 30 Yoda)",
            'reply_markup' => $key2
        ]);

        return true;
    }

    public function registration($telegram, $chatID, $username)
    {
        $member = new Members();

        // Check if the user already exists in the database
        $existingMember = $member->where('username', $username)->first();

        if (!$existingMember) {
            // User does not exist, insert new record
            $dataPost = [
                'username' => $username,
                'chat_id' => $chatID,
            ];
            $member->insert($dataPost);

            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "📝 Please type your email.",
            ]);
        } else {
            // User already exists, send a message indicating that
            $emailSubmit = Keyboard::make()
                ->setResizeKeyboard(true)
                ->setOneTimeKeyboard(true)
                ->row([
                    Keyboard::button('Next'),
                    Keyboard::button('Menu'),
                ]);

            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "You are already registered.",
                'reply_markup' => $emailSubmit
            ]);
        }
    }

    function is_valid_email($message)
    {
        // Using PHP's built-in function to check email validity
        if (filter_var($message, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function registResult($telegram, $chatID, $username, $message)
    {
        $member = new Members();

        $emailMember = $member->where('username', $username)->first();

        $memberDetail = $member->where('chat_id', $chatID)->get()->getRow();
        $id = $memberDetail->id;

        $task = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Follow twitter'),
            ]);

        if (!$emailMember) {

            $member->update($id, [
                'username' => $username,
                'chat_id' => $chatID,
                'email' => $message,
            ]);


            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Selamat $username, anda telah berhasil terdaftar di whitelist kami, Selanjutnya selesaikan task yang kami berikan untuk mendapat reward.",
                'reply_markup' => $task
            ]);
        }else {

            $member->update($id, [
                'username' => $username,
                'chat_id' => $chatID,
                'email' => $message,
            ]);

            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Selamat $username, anda telah berhasil terdaftar di whitelist kami, Selanjutnya selesaikan task yang kami berikan untuk mendapat reward.",
                'reply_markup' => $task
            ]);
        }

        return true;
    }

    public function task($telegram, $chatID, $username){
        $command = new Command();

        $task = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Follow twitter'),
                Keyboard::button('Like & retweet pinned post'),
                Keyboard::button('Follow twitter'),
            ]);

        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "your task",
            'reply_markup' => $task
        ]);
        return true;
    }

    // https://api.telegram.org/bot7159584247:AAGQhZh_1y8tvyUJcjLdKrklUThsNNtTAvc/setWebhook?url=https://ostrich-golden-monkfish.ngrok-free.app/trims
    // // Tentukan nama file untuk menyimpan data
}
