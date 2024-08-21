<?php

namespace App\Controllers;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

use App\Controllers\Start;

use App\Models\Members;
use App\Models\Email;

class Home extends BaseController
{
    public function index(){
        $telegram = new Api('7045834575:AAGsXKGV21C0XnhzQ6mxsrjlGcrpO6i_Evw');
        $response = $telegram->addCommand(\Telegram\Bot\Commands\HelpCommand::class);
        $update = $telegram->commandsHandler(true);
        dd($update);
    }

    public function telegram(){
        $begin = new Start();

        $telegram = new Api('7045834575:AAGsXKGV21C0XnhzQ6mxsrjlGcrpO6i_Evw');
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
                    $begin->start($telegram, $chatID, $username);
                    break;

                case 'Join Airdrop':
                    $begin->join($telegram, $chatID);
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
                    $this->task($telegram, $chatID);
                    break;

                // case 'Menu':
                //     $this->start($telegram, $chatID, $username);
                //     break;
            }
        }
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

    public function task($telegram, $chatID){

        $task = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Submit Answer'),
            ]);

        $telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => "your task: Beri link yang harus di selesaikan.",
            'reply_markup' => $task
        ]);
        return true;
    }
}
