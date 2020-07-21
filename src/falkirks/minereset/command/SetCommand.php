<?php
namespace falkirks\minereset\command;


use falkirks\minereset\Mine;
use falkirks\minereset\util\BlockStringParser;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$sender->hasPermission("minereset.command.set"))
            return $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);
        if (!isset($args[0]))
            return $sender->sendMessage("Sử dụng: /mine set <tên> <block và tỉ lệ chiếm>");

        $name = $args[0];
        if (!isset($this->getApi()->getMineManager()[$args[0]]))
            return $sender->sendMessage("Không thế tìm thấy khu mine {$args[0]}.");

        if(!isset($args[2]))
            return $sender->sendMessage("Bạn phải cung cấp một khối cụ thể và tỉ lệ chiếm của khối đó!");


        $sets = array_slice($args, 1);
        $save = [];

        //FIXME Allows bad ordering by treating every input as block string
        if(!array_reduce($sets, function ($carry, $curr){ return $carry && BlockStringParser::isValid($curr); }, true))
            return $sender->sendMessage(TextFormat::RED . "Một phần dữ liệu bạn nhập vào không phải là số." . TextFormat::RESET);
        if(count($sets) % 2 !== 0)
            return $sender->sendMessage(TextFormat::RED . "Dữ liệu bạn nhập vào không chính xác." . TextFormat::RESET);


        $total = 0;
        foreach ($sets as $key => $item) {
            if (strpos($item, "%")) {
                return $sender->sendMessage(TextFormat::RED . "Dữ liệu bạn nhập vào không chính xác." . TextFormat::RESET);
            }
            if ($key & 1) {
                $total += $item;
                if (isset($save[$sets[$key - 1]])) {
                    $save[$sets[$key - 1]] += $item;
                } else {
                    $save[$sets[$key - 1]] = $item;
                }
            }
        }

        if($total !== 100)
            return $sender->sendMessage(TextFormat::RED . "Tổng số phần trăm chiếm của khu mine phải là 100%, còn bạn là $total." . TextFormat::RESET);

        $this->getApi()->getMineManager()[$name]->setData($save);
        $sender->sendMessage(TextFormat::GREEN . "Thành công! Sử dụng /mine reset $name để xem kết quả.");
        return true;
    }
}