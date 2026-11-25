<?php
declare(strict_types=1);

class MessageModel
{
    private int $id = 0;
    private int $itemId = 0;
    private int $senderId = 0;
    private int $receiverId = 0;
    private string $text = '';
    private string $dateSent = '';
    private bool $isRead = false;

    // Joined data (display only).
    private string $itemName = '';
    private string $senderName = '';
    private string $receiverName = '';

    public function getId(): int { return $this->id; }
    public function getItemId(): int { return $this->itemId; }
    public function getSenderId(): int { return $this->senderId; }
    public function getReceiverId(): int { return $this->receiverId; }
    public function getText(): string { return $this->text; }
    public function getDateSent(): string { return $this->dateSent; }
    public function isRead(): bool { return $this->isRead; }
    public function getItemName(): string { return $this->itemName; }
    public function getSenderName(): string { return $this->senderName; }
    public function getReceiverName(): string { return $this->receiverName; }

    public function setId(int $id): void { $this->id = $id; }
    public function setItemId(int $id): void { $this->itemId = $id; }
    public function setSenderId(int $id): void { $this->senderId = $id; }
    public function setReceiverId(int $id): void { $this->receiverId = $id; }
    public function setText(string $text): void { $this->text = $text; }
    public function setDateSent(string $date): void { $this->dateSent = $date; }
    public function setIsRead(bool $read): void { $this->isRead = $read; }
    public function setItemName(string $name): void { $this->itemName = $name; }
    public function setSenderName(string $name): void { $this->senderName = $name; }
    public function setReceiverName(string $name): void { $this->receiverName = $name; }

    public static function fromArray(array $row): self
    {
        $message = new self();
        $message->setId((int) ($row['id_message'] ?? 0));
        $message->setItemId((int) ($row['id_item'] ?? 0));
        $message->setSenderId((int) ($row['sender_id'] ?? 0));
        $message->setReceiverId((int) ($row['receiver_id'] ?? 0));
        $message->setText((string) ($row['message_text'] ?? ''));
        $message->setDateSent((string) ($row['date_sent'] ?? ''));
        $message->setIsRead(!empty($row['is_read']));
        $message->setItemName((string) ($row['item_name'] ?? ''));
        $message->setSenderName((string) ($row['sender_name'] ?? ''));
        $message->setReceiverName((string) ($row['receiver_name'] ?? ''));
        return $message;
    }
}
