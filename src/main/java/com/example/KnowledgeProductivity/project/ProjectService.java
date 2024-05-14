package com.example.KnowledgeProductivity.project;

import org.springframework.stereotype.Service;

import java.time.LocalDate;
import java.util.Date;
import java.util.List;

@Service
public class ProjectService {

    ProjectRepository projectRepository;

    public ProjectService(ProjectRepository projectRepository) {
        this.projectRepository = projectRepository;
    }

    public Long getNumberOfProject() {
        return projectRepository.findAll().stream().count();
    }


    public Long getNoOfOverDueTasks() {
        List<Project> projects = projectRepository.findAll();
        long countOverdueTasks = 0;

        for (Project project : projects) {
            LocalDate endDate = project.getEndDate();
            if (endDate != null && endDate.isBefore(LocalDate.now())) {
                countOverdueTasks++;
            }
        }

        return countOverdueTasks;
    }

    public List<Project> getStatus() {
        return projectRepository.findAll();
    }
}
