<?php

namespace App\Controllers;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

use Dubashi\SimpleCaptcha;
use App\Models\Members;

class Home extends BaseController
{
    public function index(){
        $member = new Members();
        $captha = $member->where('chat_id', '73454745657')->get()->getResult();
        dd($captha[0]->captcha_answer);

        // create your own code
        $code = '8932';

        // Create animated GIF captcha and save it into file
        $captcha = (new SimpleCaptcha([
            'type'      => \IMAGETYPE_GIF,
            'thickness' => 4,
            'randPointPos' => true,
            'noise' => 1
        ]))
        ->create($code)       // create captcha with "code"
        ->split(1)            // split into 3 parts/frames
        ->outputFile('captcha'); // save into file "captcha.gif"

        // Get base64 data of image for own usage
        $dataBase64 = $captcha->dataUri();

        return view('main', compact('dataBase64', 'output_file'));
    }

    public function telegram(){
        $member = new Members();
        $telegram = new Api('7144370438:AAGycWjnTZJC5HpaGs_MvXBWtP1yEr1f6ao');
        // Ambil data dari input Telegram
        $data = file_get_contents('php://input');

        // Decode data JSON ke dalam bentuk array
        $decoded_data = json_decode($data, true);

        if(isset($decoded_data)){
            $username = $decoded_data['message']['from']['username'];
            $chatID = $decoded_data['message']['from']['id'];
            $message = $decoded_data['message']['text'];

            $key1 = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Join Airdrop'),
                Keyboard::button('My Balance'),
                Keyboard::button('Information'),
            ]);

            $key2 = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('Registration'),
            ]);

            if($message == '/start'){
                $telegram->sendMessage([
                    'chat_id' => $chatID,
                    'text' => "🤝 Welcome $username! I will guide you away from our airdrop.

                    1️⃣ First, click the Join Airdrop & Register button to perform the airdrop tasks and submit your information from the Register button.

                    2️⃣ You can check your balance and get your referral link by using the My Balance button. 

                    3️⃣ Please make sure that you have read the Information section.

                    🗞 Note: You can change your profile details by typing /changeprofile",
                    'reply_markup' => $key1
                ]);
            }

            if($message == 'Join Airdrop'){
                $telegram->sendMessage([
                    'chat_id' => $chatID,
                    'text' => "💻 Please perform the @airdrop tasks to earn up to 300 Yoda.

                        💠 Solve the portal captcha to join BabyYoda Telegram group.

                        💠 Follow BabyYoda on Twitter (https://twitter.com/BabyYodaonton) and retweet the pinned post by tagging 3 of your friends.

                        💠 Join our promoter channel. (https://t.me/Airdrop) (Optional » 30 Yoda)",
                    'reply_markup' => $key2
                ]);
            }

            if($message == 'Registration'){
                // Generate random numbers
                $num1 = rand(1, 10);
                $num2 = rand(1, 10);
                // Store the correct answer
                $captcha_answer = $num1 + $num2;

                $dataPost = [
                    'username' => $username,
                    'chat_id' => $chatID,
                    'captcha_answer' => $captcha_answer,
                ];
                $member->insert($dataPost);

                $telegram->sendMessage([
                    'chat_id' => $chatID,
                    'text' => "$num1 + $num2 =",
                ]);
            }

            $captha = $member->where('chat_id', $chatID)->get()->getResult();
            $capthaAnswer = $captha[0]->captcha_answer;
            if($message == $capthaAnswer){
                $telegram->sendMessage([
                    'chat_id' => $chatID,
                    'text' => "Bagus",
                ]);
            }
        }
    }
    // // Tentukan nama file untuk menyimpan data
    // $file = WRITEPATH . 'data.json';

    // // Encode array ke dalam format JSON
    // $json_encoded = json_encode($captcha_answer, JSON_PRETTY_PRINT);
    // file_put_contents($file, $json_encoded);
}
