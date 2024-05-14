package com.example.KnowledgeProductivity.user;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;
import java.util.Map;

@RestController
public class UserController {
    private final UserService userService;

    public UserController(UserService userService) {
        this.userService = userService;
    }

    @GetMapping("/dashboard/employee")
    public Long noOfEmployee(){

        return userService.findNoOfEmployee();
    }

    @GetMapping("dashboard/employeeCounts")
    public List<Map.Entry<String, Long>> noOfEmployeeCount(){

        return userService.findRoleAndEmployeeCount();
    }

    @GetMapping("/name")
    public String getName(@RequestParam Long receiverId){
        return userService.getName(receiverId);
    }
}
