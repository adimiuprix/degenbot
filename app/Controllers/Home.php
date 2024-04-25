<?php

namespace App\Controllers;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

use App\Models\Members;

class Home extends BaseController
{
    public function telegram(){
        $member = new Members();
        $telegram = new Api('7138005309:AAEhUAG0nMgDEWMrCqT5dBl82PT9A6eUFls');
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

                        $dataPost = [
                            'email' => $message,
                        ];
                        $member->insert($dataPost);

                        $this->address($telegram, $chatID, $username);
                    }else{
                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => "Bukan emmail",
                        ]);
                    }
                    break;

                case $this->is_valid_ethereum($message):
                    if ($this->is_valid_ethereum($message)){

                        $dataPost = [
                            'address' => $message,
                        ];
                        $member->insert($dataPost);

                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => "Berhasil setel alamat",
                        ]);

                    }else{
                        $telegram->sendMessage([
                            'chat_id' => $chatID,
                            'text' => "Bukan alamat",
                        ]);
                    }
                    break;
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
                    Keyboard::button('Set Email'),
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

    function is_valid_ethereum($message){
        // Ethereum address regex pattern
        $pattern = '/^0x[a-fA-F0-9]{40}$/';

        // Check if the address matches the regex pattern
        if (preg_match($pattern, $message)) {
            return true;
        } else {
            return false;
        }
    }

    public function address($telegram, $chatID, $username)
    {
        $member = new Members();

        // Check if the user already exists in the database
        $existingMember = $member->where('username', $username)->first();

        if ($existingMember) {
            // User exists, proceed with address collection
            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "📝 Please type your wallet address.",
            ]);
        } else {
            $telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => "Anda harus mendaftar terlebih dahulu sebelum menambahkan alamat dompet. Silakan gunakan perintah '/registration' untuk mendaftar.",
            ]);
        }

        return true;
    }

    // https://api.telegram.org/bot7138005309:AAEhUAG0nMgDEWMrCqT5dBl82PT9A6eUFls/setWebhook?url=https://ostrich-golden-monkfish.ngrok-free.app/trims
    // // Tentukan nama file untuk menyimpan data
}
