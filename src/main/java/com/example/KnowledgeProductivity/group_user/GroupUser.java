package com.example.KnowledgeProductivity.group_user;

import com.example.KnowledgeProductivity.groups.GroupChat;
import jakarta.persistence.*;

@Entity
@Table
public class GroupUser {

    @Id
    @SequenceGenerator(name = "groupUser_seq", sequenceName = "groupUser_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "groupUser_seq")
    private Long id;
    private Long groupId;
    private Long userId;



    public GroupUser() {
    }


    public GroupUser(Long id, Long groupId, Long userId) {
        this.id = id;
        this.groupId = groupId;
        this.userId = userId;
    }

    public GroupUser(Long groupId, long userId) {
        this.groupId = groupId;
        this.userId = userId;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Long getGroupId() {
        return groupId;
    }

    public void setGroupId(Long groupId) {
        this.groupId = groupId;
    }

    public Long getUserId() {
        return userId;
    }

    public void setUserId(Long userId) {
        this.userId = userId;
    }




}
