package com.example.KnowledgeProductivity.auth;

import jakarta.servlet.http.Cookie;
import jakarta.servlet.http.HttpServletResponse;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/auth")
@RequiredArgsConstructor
public class AuthenticationController {

    private final AuthenticationService authenticationService;

    @PostMapping("/register")
    public ResponseEntity<?> register(@RequestBody RegisterRequest request, HttpServletResponse response) {
        AuthenticationResponse authResponse = authenticationService.register(request);
        return handleAuthenticationResponse(authResponse, response);
    }

    @PostMapping("/authenticate")
    public ResponseEntity<?> authenticate(@RequestBody AuthenticationRequest request, HttpServletResponse response) {
        AuthenticationResponse authResponse = authenticationService.authenticate(request);
        return handleAuthenticationResponse(authResponse, response);
    }

    private ResponseEntity<?> handleAuthenticationResponse(AuthenticationResponse authResponse, HttpServletResponse response) {
        if (authResponse.getToken() != null && !authResponse.getToken().isEmpty()) {
            Cookie authCookie = new Cookie("jwt", authResponse.getToken());
            authCookie.setHttpOnly(true);
            authCookie.setSecure(true); // Ensure that this is set to true if you are using HTTPS
            authCookie.setPath("/");

            int cookieExpirationTimeInSeconds = 1800;
            authCookie.setMaxAge(cookieExpirationTimeInSeconds);


            response.addCookie(authCookie);


            return ResponseEntity.ok("Authentication successful");
        } else {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Authentication failed");
        }
    }
}


