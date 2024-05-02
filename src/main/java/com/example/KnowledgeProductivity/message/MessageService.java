package com.example.KnowledgeProductivity.message;

import jakarta.transaction.Transactional;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class MessageService {

    private final MessageRepository messageRepository;

    @Autowired
    public MessageService(MessageRepository messageRepository) {
        this.messageRepository = messageRepository;
    }

    public List<Message> retrieveMessages(Long recieverId) {
        return messageRepository.findAllByReceiverId(recieverId);
    }

    public void sendMessage(Message message) {
        messageRepository.save(message);
    }

    @Transactional
    public void editMessage(Long messageId, String content) {

       Optional<Message> messageToEdit = messageRepository.findById(messageId);

        messageToEdit.ifPresent(message -> message.setContent(content));
    }

    public void deleteMessage(Long messageId) {
        messageRepository.deleteById(messageId);
    }
}
