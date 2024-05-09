package com.example.KnowledgeProductivity.group_user;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class GroupUserService {


    private final GroupUserRepository groupUserRepository;

    @Autowired
    public GroupUserService(GroupUserRepository groupUserRepository) {
        this.groupUserRepository = groupUserRepository;
    }

    public void addUsersToGroup(List<Long> contactIds, Long groupChatId) {
        for (Long contactId : contactIds) {
            GroupUser groupUser = new GroupUser(groupChatId , contactId);
            groupUserRepository.save(groupUser);
        }
    }
}
