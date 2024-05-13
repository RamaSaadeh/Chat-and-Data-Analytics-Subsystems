package com.example.KnowledgeProductivity.analytic;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;

@Controller
@RequestMapping("analytic")
public class AnalyticController {

    @GetMapping
    public String displayAnalytics(){
        return "index";
    }
}
