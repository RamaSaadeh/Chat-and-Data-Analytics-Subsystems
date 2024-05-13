package com.example.KnowledgeProductivity.message;

import com.example.KnowledgeProductivity.group_user.GroupUser;
import com.example.KnowledgeProductivity.group_user.GroupUserService;
import com.example.KnowledgeProductivity.groups.GroupChat;
import com.example.KnowledgeProductivity.groups.GroupChatService;
import com.example.KnowledgeProductivity.user.User;
import com.example.KnowledgeProductivity.user.UserService;
import jakarta.servlet.http.HttpSession;
import org.springframework.beans.factory.annotation.Autowired;

import org.springframework.messaging.handler.annotation.MessageMapping;
import org.springframework.messaging.handler.annotation.SendTo;
import org.springframework.messaging.simp.SimpMessagingTemplate;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.*;


@Controller
@RequestMapping("messages")
public class MessageController {

    private final MessageService messageService;
    private final UserService userService;
    private final GroupUserService groupUserService;
    private final GroupChatService groupChatService;

    @Autowired
    private SimpMessagingTemplate template;


    Long globalUserId ;




    @Autowired
    public MessageController(MessageService messageService, UserService userService, GroupUserService groupUserService, GroupChatService groupChatService) {
        this.messageService = messageService;
        this.userService = userService;
        this.groupUserService = groupUserService;
        this.groupChatService = groupChatService;
    }

    @ResponseBody
    @GetMapping("/set")
    public void set(@RequestParam Long userId) {
        globalUserId = userId;
    }


    //serve the html page

    @GetMapping("/chat")
    public String chatPage(Model model,
                           @RequestParam(required = false) Long receiverId,
                           @RequestParam (required = false) Long groupId)  {

        List<Message> receiverAndSenderMessages = new ArrayList<>(messageService.retrieveMessages(receiverId , globalUserId));
        receiverAndSenderMessages.addAll(messageService.retrieveMessages(globalUserId, receiverId));

        receiverAndSenderMessages.sort(Comparator.comparing(Message::getTimeStamp));

        List<User> contactList = userService.getContacts(globalUserId);

        model.addAttribute("contacts", contactList);

        List<GroupUser> listOfGroups = groupUserService.getCurrentUsersGroup(globalUserId);
        List<GroupChat> groupDetails = groupChatService.getAllGroupDetails(listOfGroups);

        List<Message> groupChatMessages = messageService.getAllGroupChatMessages(groupId);
        Collections.sort(groupChatMessages, Comparator.comparing(Message::getTimeStamp));

        if(receiverId != null){
            model.addAttribute("messages", receiverAndSenderMessages);
        }

        else if(groupId != null){
            model.addAttribute("messages", groupChatMessages);
        }


        model.addAttribute("userId", globalUserId);

        model.addAttribute("groups", listOfGroups);
        model.addAttribute("groupDetails", groupDetails);
        model.addAttribute(messageService.getMesagesByGroupId(groupId));

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


    @MessageMapping("/sendMessage")
    public void sendMessage(Message message) {
        messageService.sendMessage(message); // Handle the logic to save/send message as needed

//        // Send the message to the user-specific channel
//        String userDestination = "/private/" + (message.getSenderId() + message.getReceiverId());
//
//        String groupDestination = "/group/" + message.getGroupId();

        if (message.getReceiverId() != null) {
            String userDestination = "/private/" + (message.getSenderId() + message.getReceiverId());
            template.convertAndSend(userDestination, message);
        }
        if (message.getGroupId() != null) {
            String groupDestination = "/group/" + message.getGroupId();
            template.convertAndSend(groupDestination, message);
        }
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
        template.convertAndSend("/topic/deletedMessages", messageId.toString());
    }


}
