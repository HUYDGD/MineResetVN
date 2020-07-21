<?php
namespace falkirks\minereset\command;


use falkirks\minereset\Mine;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission("minereset.command.list")) {
            $sender->sendMessage("---- DANH SÁCH KHU MINE ----");
            foreach ($this->getApi()->getMineManager() as $mine) {
                if ($mine instanceof Mine) {
                    if(!$mine->isValid()){
                        $sender->sendMessage("* (Chưa hoàn thành) Khu mine " . TextFormat::RED . $mine . TextFormat::RESET);
                    }
                    else if($mine->isResetting()){
                        $sender->sendMessage("* (Đang reset) Khu mine " . TextFormat::BLUE . $mine . TextFormat::RESET);
                    }
                    else {
                        $sender->sendMessage("* (Đã hoàn thành) Khu mine " . $mine);
                    }
                }
            }
        }
        else{
            $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);
        }
    }
}