package com.example.KnowledgeProductivity.group_user;

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
}
