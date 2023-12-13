initialPostId = null;

function toggle_class(interest_button_id) {
  let button_interest = document.getElementById(interest_button_id);
  let interestsInput = document.getElementById("input_interest");
  if (!interestsInput.value.includes(button_interest.innerText)) {
    if (interestsInput.value.trim() !== "") {
      interestsInput.value += "," + button_interest.innerText;
    } else {
      interestsInput.value += button_interest.innerText;
    }
  }
}
function getRandomQuestion() {
  var difficulty = document.getElementById("institute").value;

  // Make an asynchronous request to fetch a random question based on the difficulty
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        // Update the displayed question without refreshing the page
        document.getElementById("promptText").innerText = xhr.responseText;
      } else {
        console.error("Error fetching random question");
      }
    }
  };

  // Replace 'fetchRandomQuestion.php' with the actual server-side endpoint
  xhr.open(
    "GET",
    "prompts_logic.php?difficulty=" + encodeURIComponent(difficulty),
    true
  );
  xhr.send();
}

document.addEventListener("DOMContentLoaded", function () {
  const inputTitle = document.getElementById("input_title");
  const inputInterest = document.getElementById("input_interest");
  const textAreaPost = document.getElementById("myEditor");

  const isInputTitleBlank = !inputTitle || inputTitle.value.trim() === "";
  const isTextAreaPostBlank = !textAreaPost || textAreaPost.value.trim() === "";

  const publicButton = document.querySelector('input[name="public"]');
  const privateButton = document.querySelector('input[name="private"]');

  publicButton.addEventListener("click", function () {
    if (!isInputTitleBlank && !isTextAreaPostBlank) {
      submitFormData("Public");
    } else {
      alert("Please don't leave title and content area blank");
    }
  });

  privateButton.addEventListener("click", function () {
    if (!isInputTitleBlank && !isTextAreaPostBlank) {
      submitFormData("Private");
    } else {
      alert("Please don't leave title and content area blank");
    }
  });

  function stripHtmlTags(html) {
    var doc = new DOMParser().parseFromString(html, "text/html");
    return doc.body.textContent || "";
  }

  function submitFormData(buttonClicked) {
    const formData = new FormData();
    formData.append("title", inputTitle.value);
    formData.append("interests", inputInterest.value);

    if (initialPostId) {
      formData.append("postId", initialPostId);
    }

    const cleanedContent = stripHtmlTags(textAreaPost.value);
    const firstFiveLines = cleanedContent.split("\n").slice(0, 5).join("\n");

    formData.append("preview", firstFiveLines);
    formData.append("content", textAreaPost.value);
    formData.append("buttonClicked", buttonClicked);
    console.log(textAreaPost.value);
    const endpoint = "testing.php";

    fetch(endpoint, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
      })
      .then((result) => {
        console.log("Data submitted successfully:", result);

        parsedResult = JSON.parse(result);
        postIdToBeUsed = parsedResult.post_id;

        const viewEndpoint = `../view/view_account_file.php`;
        const url = `${viewEndpoint}?PostId=${postIdToBeUsed}`;

        window.location.href = url;
      })
      .catch((error) => {
        console.error("Error submitting data:", error);
      });
  }
});

function initializeEditing(postId) {
  initialPostId = postId;
  const formData = new FormData();
  formData.append("action", "get_word_contents");
  formData.append("postId", postId);
  const endpoint = "../view/logistic_man_arrays.php";

  fetch(endpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((result) => {
      parsedResult = JSON.parse(result);
      // const newTab = window.open();
      // newTab.document.write(result);
      insert_content(parsedResult);
    })
    .catch((error) => {
      var notFoundPost = {
        post_id: "POST_NOT_FOUND",
        title: "Post Not Found",
        author_name: "N/A",
        published_date: "N/A",
        content: "The requested post was not found.",
        interests: [],
        initial_liked: false,
        likes: 0,
        image_dir: "imgDirectory/default.jpg",
      };
      var errorObject = JSON.stringify(notFoundPost);
      insert_content(JSON.parse(errorObject));
      throw new Error("Error retrieving post contents", error);
    });
}

function insert_content(parsedContentInfo) {
  let inputTitle = document.getElementById("input_title");
  let inputInterest = document.getElementById("input_interest");
  let textAreaPost = document.getElementsByClassName("fr-element")[0];
  let textAreaPlaceholder =
    document.getElementsByClassName("fr-placeholder")[0];

  inputTitle.value = parsedContentInfo.title;

  let concatenatedInterests = "";
  if (parsedContentInfo.interests.length) {
    concatenatedInterests = parsedContentInfo.interests.join(",");
  }
  inputInterest.value = concatenatedInterests;

  textAreaPlaceholder.innerHTML = "";
  textAreaPost.innerHTML = parsedContentInfo.content;
}

function redirectToPage(index, UserId) {
  if (index == 0) {
    window.location.href = "../feed/home_page.php";
  } else if (index == 1) {
    window.location.href = `../Profile/account_view.php?UserId=${UserId}`;
  } else if (index == 2) {
    window.location.href = "../postCreation/post_creation.php";
  } else {
    var userConfirmed = confirm("Do you want to proceed with logging out?");
    if (userConfirmed) {
      window.location.href = "../logout.php";
    }
  }
}