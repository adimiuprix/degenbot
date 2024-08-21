<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Members;
use Telegram\Bot\Keyboard\Keyboard;

class Registration extends BaseController
{
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
                'reff_by' => '0',
                'ref_code' => 'eirtj9t'
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
}
