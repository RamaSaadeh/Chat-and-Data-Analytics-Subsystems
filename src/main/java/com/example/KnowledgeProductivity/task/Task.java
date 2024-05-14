package com.example.KnowledgeProductivity.task;

import jakarta.persistence.*;

import java.time.Instant;

@Entity
@Table
public class Task {

    @Id
    @SequenceGenerator(name = "message_seq", sequenceName = "message_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "message_seq")
    private Long id;
    private Long projectId;
    private Long assignedEmployeeId;
    private String taskStatus;
    private String Description;


    public Task() {
    }

    public Task(Long id, Long projectId, Long assignedEmployeeId, String taskStatus, String description) {
        this.id = id;
        this.projectId = projectId;
        this.assignedEmployeeId = assignedEmployeeId;
        this.taskStatus = taskStatus;
        Description = description;
    }

    public Task(Long projectId, Long assignedEmployeeId, String taskStatus, String description) {
        this.projectId = projectId;
        this.assignedEmployeeId = assignedEmployeeId;
        this.taskStatus = taskStatus;
        Description = description;
    }
}
