<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Telegram\Bot\Keyboard\Keyboard;

class Start extends BaseController
{
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

}
