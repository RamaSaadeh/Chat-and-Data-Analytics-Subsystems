package com.example.KnowledgeProductivity.project;

import jakarta.persistence.*;
import org.springframework.beans.factory.annotation.Autowired;

import java.time.Instant;
import java.time.LocalDate;

@Entity
@Table
public class Project {

    @Id
    @SequenceGenerator(name = "message_seq", sequenceName = "message_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "message_seq")
    private Long id;
    private String projectName;
    private String projectStatus;
    private LocalDate startDate;
    private LocalDate endDate;

    public Project() {
    }


    public Project(Long id, String projectName, String projectStatus, LocalDate startDate, LocalDate endDate) {
        this.id = id;
        this.projectName = projectName;
        this.projectStatus = projectStatus;
        this.startDate = startDate;
        this.endDate = endDate;
    }

    public Project(String projectName, String projectStatus, LocalDate startDate, LocalDate endDate) {
        this.projectName = projectName;
        this.projectStatus = projectStatus;
        this.startDate = startDate;
        this.endDate = endDate;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getProjectName() {
        return projectName;
    }

    public void setProjectName(String projectName) {
        this.projectName = projectName;
    }

    public String getProjectStatus() {
        return projectStatus;
    }

    public void setProjectStatus(String projectStatus) {
        this.projectStatus = projectStatus;
    }

    public LocalDate getStartDate() {
        return startDate;
    }

    public void setStartDate(LocalDate startDate) {
        this.startDate = startDate;
    }

    public LocalDate getEndDate() {
        return endDate;
    }

    public void setEndDate(LocalDate endDate) {
        this.endDate = endDate;
    }
}
