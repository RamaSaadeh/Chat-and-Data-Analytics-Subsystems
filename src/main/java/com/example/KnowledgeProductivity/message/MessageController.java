package com.example.KnowledgeProductivity.message;

import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.ArrayList;
import java.util.List;


@Controller
@RequestMapping("messages")
public class MessageController {

    private final MessageService messageService;
    private final HttpSession httpSession;

    @Autowired
    public MessageController(MessageService messageService, HttpSession httpSession) {
        this.messageService = messageService;
        this.httpSession = httpSession;
    }

    @GetMapping("/set")
    @ResponseBody
    public String setSession(HttpSession session, @RequestParam String userId) {
        // Set session attribute
        session.setAttribute("userId", userId); // currently set the session to 1 but needs to be changed to the person that logs in
        return "Session value set " + userId;
    }

    @GetMapping("/get")
    @ResponseBody
    public String getSession(HttpSession session) {
        String userId = (String) session.getAttribute("userId");
        return "Session value for user ID is: " + (userId != null ? userId : "Not set");
    }

    private String getUserIdFromSession(HttpSession session) {
        return (String) session.getAttribute("userId");
    }







    //serve the html page
    @GetMapping("/chat")
    public String chatPage(Model model, @RequestParam Long receiverId) {
        List<Message> receiverAndSenderMessages = new ArrayList<>(messageService.retrieveMessages(receiverId));
        receiverAndSenderMessages.addAll(messageService.retrieveMessages(Long.valueOf( getUserIdFromSession(httpSession))));

        model.addAttribute("messages", receiverAndSenderMessages);

        return "chat"; // Points to 'chat.html' Thymeleaf template
    }

//    @ResponseBody
//    @GetMapping("/retrieveMessages/{sessions}")
//    public List<List<Message>> retrieveMessages(@RequestParam() Long receiverId , @RequestParam HttpSession session) {
//        List<Message> messagesUser = messageService.retrieveMessages(getUserIdFromSession(session));
//        List<Message> recieverMessages = messageService.retrieveMessages(receiverId);
//        List<List<Message>> messagesList = new ArrayList<>();
//        messagesList.add(messagesUser);
//        messagesList.add(recieverMessages);
//        return messagesList;
//    }

    @ResponseBody
    @PostMapping("/sendMessage")
    public void sendMessages(@RequestBody Message message) {
        messageService.sendMessage(message);
    }

    @ResponseBody
    @PutMapping("/editMessage/{messageId}")
    public void editMessage(@PathVariable Long messageId,
                            @RequestParam String content) {
        messageService.editMessage(messageId,content);
    }

    @ResponseBody
    @DeleteMapping("/delete/{messageId}")
    public void deleteMessage(@PathVariable Long messageId) {
        messageService.deleteMessage(messageId);
    }
}
