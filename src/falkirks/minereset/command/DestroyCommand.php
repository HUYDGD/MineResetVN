<?php
namespace falkirks\minereset\command;


use falkirks\minereset\MineReset;
use Frago9876543210\EasyForms\forms\ModalForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class DestroyCommand extends SubCommand{

    const DESTROY_STRINGS = [
        "a",
        "b",
        "c",
        "5",
        "7",
        "-f",
        "DEATH",
        "yes",
        "15",
        "y"
    ];

    private $offset;
    private $senders;

    public function __construct(MineReset $mineReset){
        parent::__construct($mineReset);
        $this->offset = 0;
        $this->senders = [];
    }

    public function doDelete(CommandSender $sender, $name){
        unset($this->getApi()->getMineManager()[$name]);
        unset($this->senders[$sender->getName()]);
        $sender->sendMessage("Khu mine {$name[0]} đã được phá hủy.");
    }

    private function formDelete(CommandSender $sender, $name){
        $form = new class("Bạn có chắc không?", "Bạn sắp xóa khu mine có tên $name.") extends ModalForm {
            public function onSubmit(Player $player, $response) : void{
                if($response){
                    $this->parent->doDelete($player, $this->name);
                }
            }
        };
        $form->parent = $this;
        $form->name = $name;
        $sender->sendForm($form);
    }

    private function basicDelete(CommandSender $sender, $name){
        $str = DestroyCommand::DESTROY_STRINGS[$this->offset];
        $sender->sendMessage("Sử dụng: " . TextFormat::AQUA . "/mine destroy $name $str" . TextFormat::RESET);
        $sender->sendMessage("Mẹo: Bạn có thể xóa nhanh bằng cách vào file config.");
        $this->senders[$sender->getName()] = $str;

        if ($this->offset === count(DestroyCommand::DESTROY_STRINGS) - 1) {
            $this->offset = -1;
        }

        $this->offset++;
    }


    public function execute(CommandSender $sender, $commandLabel, array $args){
        if(!$sender->hasPermission("minereset.command.destroy"))
            return $sender->sendMessage(TextFormat::RED . "Bạn không có quyền để sử dụng lệnh này!" . TextFormat::RESET);

        if (!isset($args[0]))
            return $sender->sendMessage("Sử dụng: /mine destroy <tên>");

        $name = $args[0];

        if(!isset($this->getApi()->getMineManager()[$name]))
            return $sender->sendMessage("Không thế tìm thấy khu mine {$args[0]}.");

        if($sender instanceof Player && $this->formsSupported()){
            $this->formDelete($sender, $name);
        }
        else if (isset($args[1]) && isset($this->senders[$sender->getName()]) && $this->senders[$sender->getName()] === $args[1]) {
            $this->doDelete($sender, $name);
        } else {
            $this->basicDelete($sender, $name);
        }

        return true;
    }
}