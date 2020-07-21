<?php
namespace falkirks\minereset\command;


use falkirks\minereset\listener\MineCreationSession;
use falkirks\minereset\Mine;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreateCommand extends SubCommand{


    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$sender->hasPermission("minereset.command.create"))
            return $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);

        if (!($sender instanceof Player))
            return $sender->sendMessage(TextFormat::RED . "Sử dụng lệnh này trong GAME, làm ơn!" . TextFormat::RESET);

        if(!isset($args[0]))
            return $sender->sendMessage("Sử dụng: /mine create <tên>");

        if($this->getApi()->getCreationListener()->playerHasSession($sender))
            return $sender->sendMessage("Ôi bạn ơi! Bạn chưa hoàn tất việc tạo khu mine kìa, hoàn thành nó ngay đi.");

        if(isset($this->getApi()->getMineManager()[$args[0]]))
            return $sender->sendMessage("Khu mine bạn nhập đã TỒN TẠI. Vui lòng dùng \"/mine destroy {$args[0]}\" để phá hủy khu mine.");

        $this->getApi()->getCreationListener()->addSession(new MineCreationSession($args[0], $sender));
        $sender->sendMessage("Chạm vào một block để đặt điểm A.");
        return true;
    }
}