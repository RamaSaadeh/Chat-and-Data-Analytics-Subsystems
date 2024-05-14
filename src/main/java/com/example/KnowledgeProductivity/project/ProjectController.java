package com.example.KnowledgeProductivity.project;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class ProjectController {

    private final ProjectService projectService;

    public ProjectController(ProjectService projectService) {
        this.projectService = projectService;
    }

    @GetMapping("/dashboard/project")
    public Long numberOfProject() {
        return projectService.getNumberOfProject();
    }

    @GetMapping("/dashboard/overdue")
    public Long getNoOfOverdue(){
        return projectService.getNoOfOverDueTasks();
    }

    @GetMapping("/dashboard/status")
    public List<Project> getNoOfStatus(){
        return projectService.getStatus();
    }
}
