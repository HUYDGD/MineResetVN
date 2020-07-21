<?php
namespace falkirks\minereset\command;


use falkirks\minereset\exception\InvalidBlockStringException;
use falkirks\minereset\exception\InvalidStateException;
use falkirks\minereset\exception\WorldNotFoundException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ResetCommand extends SubCommand{
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$sender->hasPermission("minereset.command.reset"))
            return $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);

        if (!isset($args[0]))
            return $sender->sendMessage("Sử dụng: /mine reset <tên>");

        $mine = $this->getApi()->getMineManager()[$args[0]]; // fetch mine from the manager

        if($mine === null)
            return $sender->sendMessage("Không thế tìm thấy khu mine {$args[0]}.");

        try{
            $mine->reset();
            $sender->sendMessage("ĐANG RESET KHU {$args[0]}.");
            $this->getApi()->getResetProgressManager()->addObserver($args[0], $sender);
        }
        catch (InvalidStateException $e){
            $sender->sendMessage(TextFormat::RED . "Không thể reset lại!" . TextFormat::RESET);

            $sender->sendMessage("  --> Điều này có thể là khu mine ĐÃ ĐƯỢC RESET.");
            $sender->sendMessage("  --> Đợi vài phút và thử lại.");
            $sender->sendMessage("  --> Sau đó hãy thử khởi động lại server.");

            $sender->sendMessage("Bạn có thể dụng /mine report để báo cáo lỗi trên Github.");
        }
        catch (WorldNotFoundException $e){
            $sender->sendMessage(TextFormat::RED . "Không thể reset do không tìm thấy world." . TextFormat::RESET);

            $sender->sendMessage("  --> Có thể [{$mine->getLevelName()}] chưa được load.");
            $sender->sendMessage("  --> Có thể bạn đã đổi tên world chăng?");

            $sender->sendMessage("Bạn có thể dụng /mine report để báo cáo lỗi trên Github.");
        }
        catch(InvalidBlockStringException $e){
            $sender->sendMessage(TextFormat::RED . "Không thể reset do có lỗi trong file config." . TextFormat::RESET);

            $sender->sendMessage("  --> Điều này có nghĩa là dữ liệu ĐƯỢC LƯU trong file bị sai.");
            $sender->sendMessage("  --> Kiểm tra lại config và chắc chắn rằng nó đã đúng.");
            $sender->sendMessage("  --> Tất cả các khối cần phải là id số hoặc tên khối chính xác.");

            $sender->sendMessage("Bạn có thể dụng /mine report để báo cáo lỗi trên Github.");
        }

        return true;
    }
}