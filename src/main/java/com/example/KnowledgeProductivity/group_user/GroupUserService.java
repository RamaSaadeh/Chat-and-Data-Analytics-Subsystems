package com.example.KnowledgeProductivity.group_user;

import com.example.KnowledgeProductivity.groups.GroupChat;
import com.example.KnowledgeProductivity.groups.GroupChatRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class GroupUserService {


    private final GroupUserRepository groupUserRepository;
    private final GroupChatRepository groupChatRepository;

    @Autowired
    public GroupUserService(GroupUserRepository groupUserRepository, GroupChatRepository groupChatRepository) {
        this.groupUserRepository = groupUserRepository;
        this.groupChatRepository = groupChatRepository;
    }

    public void addUsersToGroup(List<Long> contactIds, Long groupChatId) {
        for (Long contactId : contactIds) {
            GroupUser groupUser = new GroupUser(groupChatId , contactId);
            groupUserRepository.save(groupUser);
        }
    }

    public List<GroupUser> getCurrentUsersGroup(Long userIdFromSession) {

        return groupUserRepository.findByUserId(userIdFromSession);
    }


}
