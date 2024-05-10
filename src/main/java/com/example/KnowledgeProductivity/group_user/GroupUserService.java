package com.example.KnowledgeProductivity.group_user;

import com.example.KnowledgeProductivity.groups.GroupChat;
import com.example.KnowledgeProductivity.groups.GroupChatRepository;
import com.example.KnowledgeProductivity.message.Message;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

@Service
public class GroupUserService {


    private final GroupUserRepository groupUserRepository;

    @Autowired
    public GroupUserService(GroupUserRepository groupUserRepository ) {
        this.groupUserRepository = groupUserRepository;
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

    public boolean isUserInGroup(GroupUser group ,String userId){

        Optional<GroupUser> result = groupUserRepository.findByUserIdAndGroupId(Long.parseLong(userId), group.getGroupId());
        return result.isPresent();
    }


}
