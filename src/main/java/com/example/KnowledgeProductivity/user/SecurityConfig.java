package com.example.KnowledgeProductivity.user;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.authentication.AuthenticationProvider;
import org.springframework.security.authentication.dao.DaoAuthenticationProvider;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.config.annotation.web.configurers.LogoutConfigurer;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.AuthenticationSuccessHandler;

@Configuration
@EnableWebSecurity
public class SecurityConfig {


    @Autowired
    private CustomUserService userService;

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http, HttpSecurity httpSecurity) throws Exception {
        return httpSecurity
                .csrf(AbstractHttpConfigurer::disable)
                .authorizeHttpRequests(registry-> registry.anyRequest().authenticated())
                .formLogin(form -> form
                        .successHandler(successHandler())  // Use the success handler to redirect after login
                        .permitAll())
                .logout(LogoutConfigurer::permitAll)
                .build();
    }


    @Bean
    public AuthenticationSuccessHandler successHandler() {
        return (request, response, authentication) -> {
            // Get principal from authentication object
            Object principal = authentication.getPrincipal();

            // Cast principal to UserDetails to access user information
            if (principal instanceof CustomUser) {
                CustomUser userDetails = (CustomUser) principal;

                // Assuming getUserId() is a method in your UserDetails that returns the user's ID
                long userId = userDetails.getUserId(); // Replace getUserId() with the actual method you have

                // Redirect using the dynamic user ID
                response.sendRedirect("/messages/set?userId=" + userId);
            } else {
                // Handle the case where the principal is not an instance of UserDetails
                response.sendRedirect("/login?error=true");

                System.out.println(("Attempting to redirect, principal type: " + principal.getClass().getName()));
            }
        };
    }


    @Bean
    public UserDetailsService userDetailsService() {
        return userService;
    }

    @Bean
    public AuthenticationProvider authenticationProvider() {
        DaoAuthenticationProvider provider = new DaoAuthenticationProvider();
        provider.setUserDetailsService(userService);
        provider.setPasswordEncoder(passwordEncoder());
        return provider;
    }

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }
}
