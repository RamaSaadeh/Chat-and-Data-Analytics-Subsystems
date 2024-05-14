package com.example.KnowledgeProductivity.groups;

import com.example.KnowledgeProductivity.group_user.GroupUserService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class GroupChatController {


    private final GroupChatService groupChatService;
    private final GroupUserService groupUserService;

    @Autowired
    public GroupChatController(GroupChatService groupChatService, GroupUserService groupUserService) {
        this.groupChatService = groupChatService;
        this.groupUserService = groupUserService;
    }

    @PostMapping("/createGroup")
    public void createGroupChat(@RequestBody GroupChat groupChat , @RequestParam("contactId") List<Long> contactIds) {
        groupChatService.createNewGroupChat(groupChat);
        groupUserService.addUsersToGroup(contactIds , groupChat.getId());
    }

    @GetMapping("/groupName")
    public String getGroupChatByName(@RequestParam Long groupId) {
        return groupChatService.getGroupNameById(groupId);
    }
}
