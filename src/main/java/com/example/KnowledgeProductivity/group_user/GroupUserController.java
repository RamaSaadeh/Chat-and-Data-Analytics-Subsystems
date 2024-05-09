package com.example.KnowledgeProductivity.group_user;

import com.example.KnowledgeProductivity.groups.GroupChatService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class GroupUserController {

    private final GroupUserService groupUserService;

    @Autowired
    public GroupUserController(GroupUserService groupUserService, GroupChatService groupChatService) {
        this.groupUserService = groupUserService;
    }





}
