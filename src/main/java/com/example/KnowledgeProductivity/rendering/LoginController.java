package com.example.KnowledgeProductivity.rendering;

import com.example.KnowledgeProductivity.config.JwtService;
import com.example.KnowledgeProductivity.user.User;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;

import java.net.http.HttpHeaders;

@Controller
public class LoginController {

    @GetMapping("/login")
    public String login() {

        return "login";
    }

    @GetMapping("/login-error")
    public String loginError(Model model) {
        model.addAttribute("loginError", true);
        return "login";
    }


}